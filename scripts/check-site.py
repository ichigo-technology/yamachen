#!/usr/bin/env python3
"""Fail a deploy when an internal page asset or link is missing."""

from __future__ import annotations

import re
import sys
from pathlib import Path
from urllib.parse import unquote, urlsplit


REFERENCE = re.compile(
    r'''(?<![-\w])src\s*=\s*["']([^"']+)["']|url\(\s*["']?([^"')]+)''',
    re.IGNORECASE,
)
HTML_COMMENT = re.compile(r"<!--.*?-->", re.DOTALL)
IGNORED_SCHEMES = {"data", "http", "https", "javascript", "mailto", "tel"}


def candidate_paths(root: Path, page: Path, reference: str) -> list[Path]:
    parsed = urlsplit(reference)
    if parsed.scheme.lower() in IGNORED_SCHEMES or reference.startswith(("//", "#")):
        return []

    path = unquote(parsed.path)
    if not path:
        return []

    resolved = root / path.lstrip("/") if path.startswith("/") else page.parent / path
    candidates = [resolved]
    if not resolved.suffix:
        candidates.extend([resolved.with_suffix(".html"), resolved / "index.html"])
    return candidates


def main() -> int:
    root = Path(sys.argv[1]).resolve()
    missing: list[str] = []

    for page in sorted(root.glob("*.html")):
        text = HTML_COMMENT.sub("", page.read_text(encoding="utf-8", errors="ignore"))
        for match in REFERENCE.finditer(text):
            reference = match.group(1) or match.group(2)
            candidates = candidate_paths(root, page, reference)
            if candidates and not any(candidate.exists() for candidate in candidates):
                missing.append(f"{page.relative_to(root)} -> {reference}")

    if missing:
        print("Broken internal references:", file=sys.stderr)
        for item in missing:
            print(f"  {item}", file=sys.stderr)
        return 1

    print("Site check passed: all internal HTML references resolve.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
