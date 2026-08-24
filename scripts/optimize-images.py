#!/usr/bin/env python3
"""Create visually equivalent WebP assets and update local text references."""

from __future__ import annotations

import re
from pathlib import Path

from PIL import Image


ROOT = Path(__file__).resolve().parent.parent
SCAN_GLOBS = ("*.html", "css/*.css", "dist/*.css", "js/*.js")
IMAGE_REFERENCE = re.compile(
    r'''(?P<url>(?:\.\./|\./)?(?:[A-Za-z0-9_@+,. -]+/)*[A-Za-z0-9_@+,. -]+\.(?:png|jpe?g))(?P<query>\?[^\s"')<>]*)?''',
    re.IGNORECASE,
)
MINIMUM_SIZE = 100_000
GALLERY_THUMBNAIL_WIDTH = 900


def optimized_path(source: Path) -> Path:
    return source.with_suffix(".webp")


def save_webp(source: Path, target: Path) -> None:
    with Image.open(source) as image:
        has_alpha = "A" in image.getbands()
        options = {
            "format": "WEBP",
            "method": 6,
            "quality": 90,
        }
        if has_alpha:
            options["alpha_quality"] = 100
            options["exact"] = True
        image.save(target, **options)


def rewrite_file(path: Path) -> tuple[int, int]:
    original_text = path.read_text(encoding="utf-8", errors="ignore")
    created = 0

    def replace(match: re.Match[str]) -> str:
        nonlocal created
        reference = match.group("url")
        source = (path.parent / reference).resolve()
        try:
            source.relative_to(ROOT)
        except ValueError:
            return match.group(0)

        if not source.is_file() or source.stat().st_size < MINIMUM_SIZE:
            return match.group(0)

        target = optimized_path(source)
        if not target.exists() or target.stat().st_mtime_ns < source.stat().st_mtime_ns:
            save_webp(source, target)
            created += 1

        if target.stat().st_size >= source.stat().st_size:
            target.unlink()
            return match.group(0)

        replacement = str(Path(reference).with_suffix(".webp"))
        return replacement + (match.group("query") or "")

    updated_text = IMAGE_REFERENCE.sub(replace, original_text)
    if path.suffix == ".html":
        header_end = updated_text.lower().find("</header>")
        if header_end >= 0:
            before = updated_text[:header_end]
            after = updated_text[header_end:]
            after = re.sub(
                r"<img\b(?![^>]*\bloading=)([^>]*)>",
                r'<img loading="lazy" decoding="async"\1>',
                after,
                flags=re.IGNORECASE,
            )
            updated_text = before + after

    if updated_text != original_text:
        path.write_text(updated_text, encoding="utf-8")
        return created, 1
    return created, 0


def optimize_gallery_thumbnails() -> int:
    gallery_page = ROOT / "gallery.html"
    if not gallery_page.exists():
        return 0

    thumbnail_dir = ROOT / "gallery" / "thumbs"
    thumbnail_dir.mkdir(exist_ok=True)
    created = 0

    for source in sorted((ROOT / "gallery").glob("yama-*.webp")):
        target = thumbnail_dir / source.name
        with Image.open(source) as image:
            if image.width <= GALLERY_THUMBNAIL_WIDTH:
                resized = image.copy()
            else:
                height = round(image.height * GALLERY_THUMBNAIL_WIDTH / image.width)
                resized = image.resize((GALLERY_THUMBNAIL_WIDTH, height), Image.Resampling.LANCZOS)
            target_size = Image.open(target).size if target.exists() else None
            if target_size != resized.size or target.stat().st_mtime_ns < source.stat().st_mtime_ns:
                resized.save(target, format="WEBP", method=6, quality=90)
                created += 1

    text = gallery_page.read_text(encoding="utf-8")
    text = re.sub(
        r'(<img\b[^>]*\bsrc=["\'])gallery/(yama-[^"\']+\.webp)',
        r'\1gallery/thumbs/\2',
        text,
        flags=re.IGNORECASE,
    )

    def add_thumbnail_dimensions(match: re.Match[str]) -> str:
        tag = match.group(0)
        if re.search(r"\bwidth=", tag, flags=re.IGNORECASE):
            return tag
        return tag[:-1] + ' width="900" height="600">'

    text = re.sub(
        r'<img\b(?=[^>]*\bsrc=["\']gallery/thumbs/yama-[^"\']+\.webp)[^>]*>',
        add_thumbnail_dimensions,
        text,
        flags=re.IGNORECASE,
    )

    def add_webp_dimensions(match: re.Match[str]) -> str:
        tag = match.group(0)
        if re.search(r"\bwidth=", tag, flags=re.IGNORECASE):
            return tag
        source_match = re.search(r'\bsrc=["\']([^"\']+\.webp)', tag, flags=re.IGNORECASE)
        if not source_match:
            return tag
        source = ROOT / source_match.group(1)
        if not source.exists():
            return tag
        with Image.open(source) as image:
            return tag[:-1] + f' width="{image.width}" height="{image.height}">'

    text = re.sub(
        r'<img\b(?=[^>]*\bsrc=["\'][^"\']+\.webp)[^>]*>',
        add_webp_dimensions,
        text,
        flags=re.IGNORECASE,
    )
    text = re.sub(
        r'class="popup-gallery(?: optimized-gallery-card)?"(?:\s+style="width:100%;")?',
        'class="popup-gallery optimized-gallery-card" style="width:100%;"',
        text,
    )
    gallery_page.write_text(text, encoding="utf-8")
    return created


def main() -> None:
    files: set[Path] = set()
    for pattern in SCAN_GLOBS:
        files.update(ROOT.glob(pattern))

    created = changed = 0
    for path in sorted(files):
        file_created, file_changed = rewrite_file(path)
        created += file_created
        changed += file_changed

    created += optimize_gallery_thumbnails()

    print(f"Created {created} WebP assets; updated {changed} text files.")


if __name__ == "__main__":
    main()
