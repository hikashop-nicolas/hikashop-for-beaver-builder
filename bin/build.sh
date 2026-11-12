#!/usr/bin/env bash
# Builds build/hikashop-for-beaver-builder/ and build/hikashop-for-beaver-builder.zip from the files .distignore keeps
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
out="$root/build/hikashop-for-beaver-builder"
rm -rf "$root/build"
mkdir -p "$out"
rsync -a --exclude-from="$root/.distignore" --exclude=/build --exclude=/bin "$root/" "$out/"
( cd "$root/build" && zip -qr hikashop-for-beaver-builder.zip hikashop-for-beaver-builder )
echo "$root/build/hikashop-for-beaver-builder.zip"
