#!/usr/bin/env bash
# Smoke-check: run php -l on all key PHP files to verify syntax.
# Usage: bash scripts/smoke-check.sh

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
EXIT_CODE=0

FILES=(
    "$ROOT/index.php"
    "$ROOT/config.php"
    "$ROOT/clean.php"
    "$ROOT/img.php"
    "$ROOT/classes/account.class.php"
    "$ROOT/classes/character.class.php"
    "$ROOT/classes/config.class.php"
    "$ROOT/classes/core.class.php"
    "$ROOT/classes/email.class.php"
    "$ROOT/classes/mysql.class.php"
    "$ROOT/classes/smtp.class.php"
    "$ROOT/classes/system.class.php"
    "$ROOT/classes/world.class.php"
    "$ROOT/classes/mail-signature.class.php"
    "$ROOT/classes/mail-signature.config.php"
)

# Also include database.class.php if it exists (PR-2+)
if [[ -f "$ROOT/classes/database.class.php" ]]; then
    FILES+=("$ROOT/classes/database.class.php")
fi

echo "=== PHP Syntax Check ==="
for f in "${FILES[@]}"; do
    if [[ ! -f "$f" ]]; then
        echo "SKIP  $f (not found)"
        continue
    fi
    if php -l "$f" 2>&1 | grep -q "No syntax errors"; then
        echo "OK    $f"
    else
        echo "FAIL  $f"
        php -l "$f"
        EXIT_CODE=1
    fi
done

echo ""
if [[ $EXIT_CODE -eq 0 ]]; then
    echo "All files passed syntax check."
else
    echo "Some files have syntax errors!"
fi

exit $EXIT_CODE
