#!/usr/bin/env python3
"""Télécharge les polices de la charte et régénère assets/css/fonts.css.

Les quatre familles (Bodoni Moda, Oswald, Atkinson Hyperlegible, IBM Plex Mono)
sont sous SIL Open Font License 1.1 et sont auto-hébergées : le site public
n'appelle jamais fonts.googleapis.com.

Usage :
    python3 bin/fetch-fonts.py
"""

import os
import re
import subprocess
import sys

UA = (
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 "
    "(KHTML, like Gecko) Chrome/124.0 Safari/537.36"
)

RACINE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DOSSIER_FONTES = os.path.join(RACINE, "assets", "fonts")
FICHIER_CSS = os.path.join(RACINE, "assets", "css", "fonts.css")

# Graisses reprises de la planche de charte (option 2e).
FAMILLES = {
    "bodoni-moda": "family=Bodoni+Moda:ital,opsz,wght@0,6..96,400;0,6..96,700;0,6..96,900;1,6..96,400",
    "oswald": "family=Oswald:wght@300;400;500;600",
    "atkinson": "family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400",
    "plex-mono": "family=IBM+Plex+Mono:wght@400;500;600",
}

SOUS_ENSEMBLES = {"latin", "latin-ext"}

ORDRE = {
    "Bodoni Moda": 0,
    "Oswald": 1,
    "Atkinson Hyperlegible": 2,
    "IBM Plex Mono": 3,
}


def curl(url, destination=None):
    commande = ["curl", "-sL", "-m", "30", "-A", UA, url]

    if destination:
        commande += ["-o", destination]
        subprocess.run(commande, check=True)
        return None

    return subprocess.run(commande, check=True, capture_output=True).stdout.decode()


def main():
    os.makedirs(DOSSIER_FONTES, exist_ok=True)
    fontes = []

    for slug, requete in FAMILLES.items():
        css = curl("https://fonts.googleapis.com/css2?%s&display=swap" % requete)
        blocs = re.findall(
            r"/\*\s*([\w\-\[\]]+)\s*\*/\s*@font-face\s*\{(.*?)\}", css, re.S
        )

        if not blocs:
            sys.exit("Aucune déclaration @font-face reçue pour %s." % slug)

        for sous_ensemble, corps in blocs:
            if sous_ensemble not in SOUS_ENSEMBLES:
                continue

            url = re.search(r"url\((https://[^)]+\.woff2)\)", corps).group(1)
            famille = re.search(r"font-family:\s*'([^']+)'", corps).group(1)
            style = re.search(r"font-style:\s*([^;]+);", corps).group(1).strip()
            graisse = re.search(r"font-weight:\s*([^;]+);", corps).group(1).strip()
            plage = re.search(r"unicode-range:\s*([^;]+);", corps).group(1).strip()

            nom = "%s-%s-%s-%s.woff2" % (
                slug,
                graisse.replace(" ", "-"),
                style,
                sous_ensemble,
            )
            curl(url, os.path.join(DOSSIER_FONTES, nom))

            fontes.append(
                {
                    "famille": famille,
                    "style": style,
                    "graisse": graisse,
                    "fichier": nom,
                    "plage": plage,
                    "sous_ensemble": sous_ensemble,
                }
            )

    fontes.sort(
        key=lambda f: (
            ORDRE[f["famille"]],
            f["graisse"],
            f["style"],
            f["sous_ensemble"],
        )
    )

    lignes = [
        "/* Polices auto-hébergées — générées depuis Google Fonts (SIL Open Font License 1.1).",
        "   Voir README.md § Polices pour la régénération. */",
        "",
    ]

    for fonte in fontes:
        lignes += [
            "@font-face {",
            "  font-family: '%s';" % fonte["famille"],
            "  font-style: %s;" % fonte["style"],
            "  font-weight: %s;" % fonte["graisse"],
            "  font-display: swap;",
            "  src: url('../fonts/%s') format('woff2');" % fonte["fichier"],
            "  unicode-range: %s;" % fonte["plage"],
            "}",
            "",
        ]

    with open(FICHIER_CSS, "w", encoding="utf-8") as sortie:
        sortie.write("\n".join(lignes))

    print("%d déclarations écrites dans %s" % (len(fontes), FICHIER_CSS))


if __name__ == "__main__":
    main()
