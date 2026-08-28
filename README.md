# theatre.mehdiguiraud.info

Thème WordPress classique du site **Le Refoulement de Freud**, décliné de la charte
graphique « Le Refoulement de Freud » (direction 2 « true crime — noir, papier, rouge
dossier », accueil option **3a** « déchirure rouge en couverture, puis dossier papier
tramé »).

Le dépôt **est** le thème : `style.css`, `functions.php` et les gabarits sont à la
racine.

Prérequis : WordPress 6.5+, PHP 8.0+.

## Installation

**Par archive** (recommandé pour un site distant) :

```sh
bin/build-package.sh            # → dist/refoulement-1.0.0.zip
```

Puis Apparence → Thèmes → Ajouter → Téléverser un thème → activer
« Le Refoulement de Freud ».

Le script recopie le dépôt dans un dossier `refoulement/` — WordPress refuse une
archive dont la racine n'est pas un dossier unique contenant `style.css` — exclut
`.git/`, `dist/` et les aperçus, vérifie la présence de l'en-tête de thème, des
polices et la syntaxe PHP de ce qui part dans l'archive, puis compresse. Passer un
numéro en argument (`bin/build-package.sh 1.1.0`) force la version ; sans argument,
elle est lue dans `style.css`.

**En développement** : cloner le dépôt dans `wp-content/themes/refoulement/`, ou y
créer un lien symbolique.

## Correspondance charte → gabarits

| Planche | Gabarit |
| --- | --- |
| 3a — accueil | `front-page.php` + `template-parts/couverture.php` |
| 2d — article | `single.php` |
| 3a (grille « Pièces au dossier ») | `index.php`, `search.php` |
| 1f — liste des entretiens | `archive-entretien.php` |
| 1g — page entretien | `single-entretien.php` |
| 1h — « Le projet » | `page-manifeste.php` (modèle « Le projet — manifeste ») |
| 1h — « À propos » | `page-portrait.php` (modèle « À propos — portrait ») |
| 1i — mobile | requêtes média de `assets/css/theme.css`, § 14 |
| 2e — palette et typographie | `theme.json` + jetons CSS de `assets/css/theme.css`, § 1 |

Les planches 1a–1e appartiennent à la direction 1, explicitement remplacée par la
direction 2. Seules les **structures** de 1f, 1g et 1h ont été reprises : couleurs et
polices sont celles de 2e.

## Palette — les trois rouges ne sont pas interchangeables

| Jeton | Valeur | Emploi |
| --- | --- | --- |
| `--rouge-dossier` | `#B8121B` | aplats, boutons, filets. **Jamais** du texte courant |
| `--rouge-texte` | `#8E1017` | texte rouge sur papier (7,4:1) : liens, cotes, surtitres |
| `--rouge-nuit` | `#E0575E` | rouge sur `#0B0B0C` uniquement (5,3:1) : nav active, surtitres sur noir |

Deux papiers, à ne pas confondre : `--papier` `#EAE4D8` (fond de lecture) et
`--papier-couverture` `#DCD5C4` (surface de couverture, avec la trame).

## Modèle de contenu

- **Journal de création** → articles natifs, cotes `N-xx`.
- **Entretiens** → type de contenu `entretien`, cotes `E-xx`, archive `/entretiens/`.
- L'accueil agrège les deux (`refoulement_types_du_dossier()`, filtrable).

Métadonnées, boîte « Pièce au dossier » (colonne de droite de l'éditeur) :

| Clé | Types | Rôle |
| --- | --- | --- |
| `_refoulement_numero` | article, entretien | entier composant la cote (`14` → `N-14`) |
| `_refoulement_cote` | article, entretien | cote forcée, ex. `P-02` |
| `_refoulement_duree` | entretien | `71 min` |
| `_refoulement_periode` | entretien | `Avril 2026` |
| `_refoulement_citation` | entretien | phrase en exergue, sans guillemets |
| `_refoulement_source` | entretien | attribution (fonction, jamais un nom sans accord écrit) |

Sans numéro renseigné, la cote retombe sur l'identifiant du contenu.

## Compositions de blocs

Catégorie « Le Refoulement de Freud » dans l'insertion :

- **Entretien — tour de parole** : locuteur en marge, réponse en colonne (planche 1g).
  À répéter pour chaque échange plutôt que de composer le dialogue à la main.
- **Citation versée au dossier** : Bodoni italique, filet rouge, attribution en cote.
- **Note en marge** : apparat critique. Dans un article, la note flotte dans la marge
  de 250 px ; sous 1100 px elle repasse dans le fil du texte.
- **Calendrier du projet** : trois repères datés (planche 1h).
- **Fac-similé** : document versé, monté comme un tirage épinglé.

## Réglages

Apparence → Personnaliser → **Le Refoulement de Freud** :

- *Couverture d'accueil* : numéro de dossier, état, titre coupé par la déchirure
  (partie rouge / partie papier), chapô, deux boutons, compteurs (`valeur|libellé`,
  une par ligne, trois au plus), deux découpes et leur légende.
- *Recevoir les pièces* : le thème ne traite aucune inscription, il délègue. Cinq
  sources, par ordre de priorité :

  1. le filtre `refoulement_lettre_formulaire`, pour brancher une extension ;
  2. **l'identifiant d'un formulaire du plugin Brevo** (Brevo → Formulaires) : le
     formulaire est rendu dans la page par le shortcode `sibwp_form` et habillé par
     le thème. Demande une clé API enregistrée dans le plugin — sans elle le
     shortcode n'existe pas. Le thème ne rend jamais un shortcode saisi à la main :
     il valide un entier positif et construit `[sibwp_form id=N]` lui-même ;
  3. l'adresse publique du formulaire intégré Brevo (`src` du code iframe), limitée
     à HTTPS sur `sibforms.com` ;
  4. une adresse de traitement fournie par un autre service d'envoi ;
  5. repli : lien de contact par courriel. Sans adresse de contact non plus, le
     bandeau ne s'affiche pas.

  Les cas 2 et 3 sont complémentaires : si la clé API manque ou si l'identifiant
  désigne un formulaire inexistant, le thème retombe seul sur l'iframe. Garder les
  deux réglages renseignés donne un filet de sécurité.

  L'iframe porte la classe `no-lazyload`, mot-clé d'exclusion de Smush : sans elle,
  le chargement différé remplace `src` par un SVG transparent qu'il ne restaure
  jamais et le bandeau reste vide.

  L'apparence fine du formulaire natif (textes, champs, messages) se règle dans
  l'éditeur de formulaire de Brevo ; le thème ne cadre que le bloc, les champs et le
  bouton pour les accorder au bandeau noir.

Menus : *principal* (bandeau noir) et *pied*.

## Polices

Les quatre familles — Bodoni Moda, Oswald, Atkinson Hyperlegible, IBM Plex Mono, toutes
sous SIL Open Font License 1.1 — sont **auto-hébergées** dans `assets/fonts/`. Le site
public n'appelle jamais `fonts.googleapis.com`. Trois fichiers sont préchargés
(Bodoni 900, Oswald 500, Atkinson 400).

Pour régénérer les fichiers et `assets/css/fonts.css` :

```sh
python3 bin/fetch-fonts.py
```

## Règle éditoriale sur les images

Reprise de la planche 2e, elle vaut pour toute image versée au site :

> Découpes N&B contrastées, polaroïds épinglés, fils rouges, trames de papier et
> fac-similés. **Pas de visages de victimes ni de scènes reconstituées** — silhouettes,
> objets, documents.

Le thème applique un traitement noir et blanc contrasté aux découpes de couverture, aux
portraits et aux images à la une.

## Structure

```
assets/css/    fonts.css (généré), theme.css (site), editor.css (éditeur)
assets/fonts/  woff2 auto-hébergés
assets/js/     theme.js — bascule du menu sur petit écran
bin/           fetch-fonts.py, build-package.sh
inc/           setup, enqueue, post-types, meta, template-tags, queries, customizer, patterns
template-parts/ bandeau-nav, couverture, piece-carte, lettre, aucune-piece
```
