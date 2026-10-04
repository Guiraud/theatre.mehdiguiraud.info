#!/usr/bin/env bash
#
# Construit l'archive installable du thème.
#
# WordPress attend un zip contenant un dossier unique à la racine, portant le
# slug du thème et contenant style.css. Le dépôt étant lui-même le thème, on
# recopie son contenu dans dist/refoulement/ avant de compresser.
#
# Usage :
#   bin/build-package.sh            → dist/refoulement-<version>.zip
#   bin/build-package.sh 1.1.0      → force le numéro de version

set -euo pipefail

RACINE="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SLUG="refoulement"
DIST="$RACINE/dist"

if [ $# -ge 1 ]; then
	VERSION="$1"
else
	VERSION="$(sed -n 's/^Version:[[:space:]]*//p' "$RACINE/style.css" | head -1 | tr -d '[:space:]')"
fi

if [ -z "${VERSION:-}" ]; then
	echo "Version introuvable dans style.css." >&2
	exit 1
fi

ARCHIVE="$DIST/$SLUG-$VERSION.zip"

rm -rf "$DIST/$SLUG" "$ARCHIVE"
mkdir -p "$DIST/$SLUG"

# Contenu du thème. Tout le reste (dépôt git, aperçus, dist) est exclu.
rsync -a \
	--exclude '.git/' \
	--exclude '.gitignore' \
	--exclude '.gitattributes' \
	--exclude '.DS_Store' \
	--exclude '.continues-handoff.md' \
	--exclude '.mcp_memory/' \
	--exclude 'dist/' \
	--exclude '.worktrees/' \
	--exclude 'bin/' \
	--exclude 'docs/' \
	--exclude 'tests/' \
	--exclude 'node_modules/' \
	--exclude 'apercu-*.html' \
	--exclude '*.log' \
	"$RACINE/" "$DIST/$SLUG/"

# Contrôles avant compression : sans style.css ni son en-tête, WordPress refuse
# l'archive avec « Le paquet n’a pas pu être installé ».
if [ ! -f "$DIST/$SLUG/style.css" ]; then
	echo "style.css absent du paquet." >&2
	exit 1
fi

if ! grep -q '^Theme Name:' "$DIST/$SLUG/style.css"; then
	echo "En-tête « Theme Name » absent de style.css." >&2
	exit 1
fi

if [ ! -f "$DIST/$SLUG/index.php" ] || [ ! -f "$DIST/$SLUG/functions.php" ]; then
	echo "index.php ou functions.php absent du paquet." >&2
	exit 1
fi

if [ -z "$(find "$DIST/$SLUG/assets/fonts" -name '*.woff2' -print -quit)" ]; then
	echo "Polices absentes : lancer python3 bin/fetch-fonts.py." >&2
	exit 1
fi

# Vérifie la syntaxe PHP de ce qui part réellement dans l'archive.
if command -v php >/dev/null 2>&1; then
	find "$DIST/$SLUG" -name '*.php' -print0 | xargs -0 -n1 php -l >/dev/null
fi

( cd "$DIST" && zip -rq "$ARCHIVE" "$SLUG" -x '*.DS_Store' )
rm -rf "$DIST/$SLUG"

echo "$ARCHIVE"
du -h "$ARCHIVE" | cut -f1
