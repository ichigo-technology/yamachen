#!/usr/bin/env bash
set -euo pipefail

repository_root="$(cd "$(dirname "$0")/.." && pwd)"
publish_dir="$repository_root/.netlify-dist"

mkdir -p "$publish_dir"

rsync -a --delete \
  --exclude '/.git/' \
  --exclude '/.github/' \
  --exclude '/.netlify/' \
  --exclude '/.netlify-dist/' \
  --exclude '/stats/' \
  --exclude '/scripts/' \
  --exclude '/src/' \
  --exclude '/partials/' \
  --exclude '/bootstrap/bootstrap.zip' \
  --exclude '*.php' \
  --exclude '*.scss' \
  --exclude '.DS_Store' \
  --exclude '/netlify.toml' \
  "$repository_root/" "$publish_dir/"

# Browsers request this conventional path even though the pages also declare
# images/favicon.ico explicitly.
cp "$publish_dir/images/favicon.ico" "$publish_dir/favicon.ico"

python3 "$repository_root/scripts/check-site.py" "$publish_dir"
