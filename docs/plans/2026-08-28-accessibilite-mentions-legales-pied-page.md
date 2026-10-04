# Accessible Newsletter and Legal Footer Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Restaurer les labels accessibles du formulaire Brevo, publier les contenus légaux validés et ajouter au pied de page des liens fiables vers les pages légales et la newsletter.

**Architecture:** Le thème post-traite uniquement le HTML du shortcode Brevo et restaure les associations `label`/champ sans élargir la liste blanche HTML de WordPress ou modifier le plugin. Les contenus légaux sont préparés comme sources de déploiement hors du paquet du thème, publiés dans des pages WordPress, puis résolus par les API WordPress depuis un pied de page en grille responsive.

**Tech Stack:** PHP 8.0+, WordPress 6.5+, `DOMDocument`, HTML sémantique, CSS natif, tests PHP autonomes, SSH/PHP CLI OVH, Chrome responsive.

---

## Contexte vérifié

- Le shortcode Brevo 3.3.5 supprime ses propres `<label>` parce que
  `SIB_Manager::SIB_ATTRIBUTE` n'autorise pas cet élément.
- `DOMDocument` est disponible localement et sur le PHP 8.3 de production.
- La page WordPress 3, « Confidentialité — Newsletter », est publiée et définie
  comme politique de confidentialité.
- Aucun menu n'est assigné et aucune page « Mentions légales » n'existe.
- Contact Form 7 est actif, mais aucun formulaire n'est intégré à un contenu
  publié. Les commentaires sont ouverts par défaut et aucun commentaire n'est
  actuellement approuvé.
- Aucun outil d'audience ou de publicité n'est actif. Wordfence est actif,
  traite l'IP pour le pare-feu, mais son trafic en direct est désactivé.
- Les bases Brevo sont hébergées dans l'Union européenne :
  <https://help.brevo.com/hc/fr/articles/360001005510-Lieux-de-stockage-des-donn%C3%A9es>.
- Coordonnées officielles de l'hébergeur : OVH SAS, 2 rue Kellermann, 59100
  Roubaix, RCS Lille Métropole 424 761 419 00045 ; support France 1007,
  international +33 9 72 10 10 07 :
  <https://www.ovhcloud.com/fr/terms-and-conditions/> et
  <https://docs.ovhcloud.com/fr/guides/account-and-service-management/account-information/faq-support>.

## Task 1: Restaurer les labels accessibles du formulaire Brevo

**Skills:** @superpowers:test-driven-development

**Files:**
- Create: `inc/newsletter.php`
- Modify: `functions.php:14-21`
- Modify: `template-parts/lettre.php:38-43`
- Modify: `tests/test-newsletter-embed.php:64-185`

**Step 1: Write the failing accessibility test**

Require `inc/newsletter.php` from the standalone test, and replace the native
form fixture with Brevo's observed, label-stripped structure:

```php
$forme_html = '<form id="sib_signup_form_1" class="sib_signup_form">'
	. '<input type="hidden" name="sib_form_id" value="1">'
	. '<p class="sib-email-area">Email Address*<input type="email" name="email" required="required"></p>'
	. '<p class="sib-FIRSTNAME-area">FIRSTNAME<input type="text" name="FIRSTNAME"></p>'
	. '<p><input type="submit" value="Subscribe"></p>'
	. '<style>.sib_signup_form{display:block}</style>'
	. '</form>';

$accessible = refoulement_accessibiliser_formulaire_brevo( $forme_html );
expect_contains( '<label for="refoulement-brevo-1-email">Email Address*</label>', $accessible, 'Le texte visible doit redevenir le label du champ e-mail.' );
expect_contains( 'id="refoulement-brevo-1-email"', $accessible, 'Le champ e-mail doit recevoir un identifiant stable.' );
expect_contains( 'type="hidden" name="sib_form_id" value="1"', $accessible, 'Les champs techniques restent intacts.' );
expect_contains( '<style>.sib_signup_form{display:block}</style>', $accessible, 'Le CSS produit par Brevo doit être préservé.' );
expect_same( $accessible, refoulement_accessibiliser_formulaire_brevo( $accessible ), 'La transformation doit être idempotente.' );

$deja_accessible = '<form id="sib_signup_form_2"><p><label for="email-2">Adresse électronique</label><input id="email-2" type="email" name="email"></p></form>';
expect_same( $deja_accessible, refoulement_accessibiliser_formulaire_brevo( $deja_accessible ), 'Un formulaire déjà accessible doit rester strictement inchangé.' );
```

**Step 2: Run test to verify RED**

Run: `php tests/test-newsletter-embed.php`

Expected: FAIL because `refoulement_accessibiliser_formulaire_brevo()` does not
exist or because the fixture still has no associated labels.

**Step 3: Implement the minimal post-processor**

Create `inc/newsletter.php` with a pure function:

```php
<?php
defined( 'ABSPATH' ) || exit;

function refoulement_accessibiliser_formulaire_brevo( $html ) {
	$html = (string) $html;

	if ( '' === trim( $html ) || ! class_exists( 'DOMDocument' ) ) {
		return $html;
	}

	$document = new DOMDocument( '1.0', 'UTF-8' );
	$precedent = libxml_use_internal_errors( true );
	$charge = $document->loadHTML(
		'<?xml encoding="UTF-8"><div id="refoulement-brevo-fragment">' . $html . '</div>',
		LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
	);
	libxml_clear_errors();
	libxml_use_internal_errors( $precedent );

	if ( ! $charge ) {
		return $html;
	}

	$xpath = new DOMXPath( $document );
	$champs = $xpath->query( '//*[@id="refoulement-brevo-fragment"]//*[self::input or self::select or self::textarea]' );
	$modifie = false;
	$index = 0;

	foreach ( $champs as $champ ) {
		$type = strtolower( $champ->getAttribute( 'type' ) );
		if ( 'input' === strtolower( $champ->nodeName ) && in_array( $type, array( 'hidden', 'submit', 'button', 'reset', 'image', 'checkbox', 'radio' ), true ) ) {
			continue;
		}

		$id = $champ->getAttribute( 'id' );
		if ( '' !== $id && $xpath->query( './/label[@for="' . $id . '"]', $document->getElementById( 'refoulement-brevo-fragment' ) )->length ) {
			continue;
		}

		if ( '' !== $champ->getAttribute( 'aria-label' ) || '' !== $champ->getAttribute( 'aria-labelledby' ) || 'label' === strtolower( $champ->parentNode->nodeName ) ) {
			continue;
		}

		$texte = null;
		foreach ( $champ->parentNode->childNodes as $noeud ) {
			if ( $noeud === $champ ) {
				break;
			}
			if ( XML_TEXT_NODE === $noeud->nodeType && '' !== trim( $noeud->nodeValue ) ) {
				$texte = $noeud;
			}
		}

		if ( ! $texte ) {
			continue;
		}

		++$index;
		$formulaire = $champ->parentNode;
		while ( $formulaire && 'form' !== strtolower( $formulaire->nodeName ) ) {
			$formulaire = $formulaire->parentNode;
		}
		$form_id = $formulaire ? preg_replace( '/^sib_signup_form_/', '', $formulaire->getAttribute( 'id' ) ) : 'form';
		$nom = preg_replace( '/[^a-z0-9_-]+/i', '-', $champ->getAttribute( 'name' ) ?: (string) $index );
		$id = $id ?: 'refoulement-brevo-' . trim( $form_id, '-' ) . '-' . trim( $nom, '-' );
		$champ->setAttribute( 'id', $id );

		$label = $document->createElement( 'label' );
		$label->setAttribute( 'for', $id );
		$label->appendChild( $document->createTextNode( trim( $texte->nodeValue ) ) );
		$texte->parentNode->replaceChild( $label, $texte );
		$modifie = true;
	}

	if ( ! $modifie ) {
		return $html;
	}

	$fragment = $document->getElementById( 'refoulement-brevo-fragment' );
	$resultat = '';
	foreach ( $fragment->childNodes as $enfant ) {
		$resultat .= $document->saveHTML( $enfant );
	}

	return $resultat;
}
```

During implementation, adjust the XPath label lookup if `DOMDocument` does not
register the wrapper ID without a DTD. Do not fall back to mutating
`$allowedposttags` and do not patch `mailin/sendinblue.php`.

Require the file from `functions.php`, then change the shortcode branch to:

```php
$refoulement_natif = refoulement_accessibiliser_formulaire_brevo(
	trim( do_shortcode( sprintf( '[sibwp_form id=%d]', $refoulement_form_id ) ) )
);
```

**Step 4: Run test to verify GREEN**

Run: `php tests/test-newsletter-embed.php`

Expected: `OK — intégration Brevo validée.`

**Step 5: Run syntax checks**

Run: `php -l inc/newsletter.php && php -l template-parts/lettre.php`

Expected: two `No syntax errors detected` lines.

**Step 6: Commit**

```bash
git add functions.php inc/newsletter.php template-parts/lettre.php tests/test-newsletter-embed.php
git commit -m "fix(lettre): restaurer les labels du formulaire Brevo"
```

## Task 2: Résoudre les pages légales avec les API WordPress

**Skills:** @superpowers:test-driven-development

**Files:**
- Create: `inc/footer.php`
- Create: `tests/test-footer.php`
- Modify: `functions.php:14-22`
- Modify: `inc/customizer.php:51-188,209-279`

**Step 1: Write the failing URL-resolution test**

Create a standalone test with stubs for `get_theme_mod()`, `get_post_status()`,
`get_permalink()`, `get_privacy_policy_url()` and `home_url()`. Assert:

```php
$GLOBALS['refoulement_pages'] = array( 42 => 'publish', 43 => 'draft' );
$GLOBALS['refoulement_theme_mods']['refoulement_mentions_legales_page_id'] = 42;

$liens = refoulement_liens_pied();
expect_same( 'https://example.test/mentions-legales/', $liens['mentions']['url'], 'La page publiée doit être liée.' );
expect_same( 'https://example.test/confidentialite/', $liens['confidentialite']['url'], 'La politique WordPress doit être liée.' );
expect_same( 'https://example.test/#recevoir-les-pieces', $liens['newsletter']['url'], 'La newsletter doit viser l’ancre de l’accueil.' );

$GLOBALS['refoulement_theme_mods']['refoulement_mentions_legales_page_id'] = 43;
$liens = refoulement_liens_pied();
expect_same( false, isset( $liens['mentions'] ), 'Une page en brouillon ne doit pas produire de lien.' );
```

**Step 2: Run test to verify RED**

Run: `php tests/test-footer.php`

Expected: FAIL because `inc/footer.php` and `refoulement_liens_pied()` do not exist.

**Step 3: Add the footer setting**

In `refoulement_reglages()` add:

```php
'refoulement_mentions_legales_page_id' => array(
	'defaut'      => 0,
	'label'       => __( 'Page des mentions légales', 'refoulement' ),
	'type'        => 'page',
	'section'     => 'refoulement_pied',
	'description' => __( 'Seule une page publiée est affichée dans le pied de page.', 'refoulement' ),
),
```

Register `refoulement_pied` in the existing customizer panel. Sanitize `page`
with `absint`, and map it to the control type `dropdown-pages`.

**Step 4: Implement the link resolver**

Create `inc/footer.php`:

```php
<?php
defined( 'ABSPATH' ) || exit;

function refoulement_liens_pied() {
	$liens = array();
	$page_mentions = absint( refoulement_reglage( 'refoulement_mentions_legales_page_id' ) );

	if ( $page_mentions && 'publish' === get_post_status( $page_mentions ) ) {
		$liens['mentions'] = array(
			'label' => __( 'Mentions légales', 'refoulement' ),
			'url'   => get_permalink( $page_mentions ),
		);
	}

	$confidentialite = get_privacy_policy_url();
	if ( '' !== $confidentialite ) {
		$liens['confidentialite'] = array(
			'label' => __( 'Confidentialité', 'refoulement' ),
			'url'   => $confidentialite,
		);
	}

	$liens['newsletter'] = array(
		'label' => __( 'Recevoir les pièces', 'refoulement' ),
		'url'   => home_url( '/#recevoir-les-pieces' ),
	);

	return $liens;
}
```

Require it from `functions.php` after `inc/customizer.php` because it calls
`refoulement_reglage()`.

**Step 5: Run test to verify GREEN**

Run: `php tests/test-footer.php && php tests/test-newsletter-embed.php`

Expected: both scripts print `OK` and exit 0.

**Step 6: Commit**

```bash
git add functions.php inc/customizer.php inc/footer.php tests/test-footer.php
git commit -m "feat(pied): résoudre les liens légaux WordPress"
```

## Task 3: Construire le pied de page et l'ancre newsletter

**Skills:** @superpowers:test-driven-development @ui-ux-pro-max

**Files:**
- Modify: `tests/test-footer.php`
- Modify: `tests/test-newsletter-embed.php`
- Modify: `footer.php:12-50`
- Modify: `template-parts/lettre.php:52`
- Modify: `assets/css/theme.css:957-960,1219-1295,1399-1457`

**Step 1: Write failing markup tests**

Add assertions that the newsletter markup contains:

```php
expect_contains( 'id="recevoir-les-pieces"', $markup_shortcode, 'Le bandeau doit exposer une ancre stable.' );
```

Render `footer.php` in `tests/test-footer.php` using no-op stubs for the menu,
widget and footer hooks, then assert:

```php
expect_contains( 'class="pied__interieur"', $footer, 'Le pied doit garder son conteneur.' );
expect_contains( 'aria-label="Informations légales"', $footer, 'Les liens légaux doivent former une navigation nommée.' );
expect_contains( '>Mentions légales</a>', $footer, 'Le lien vers les mentions doit être rendu.' );
expect_contains( '>Confidentialité</a>', $footer, 'Le lien de confidentialité doit être rendu.' );
expect_contains( 'class="pied__appel"', $footer, 'La newsletter doit être un appel à l’action distinct.' );
expect_contains( 'href="https://example.test/#recevoir-les-pieces"', $footer, 'L’appel doit fonctionner hors de l’accueil.' );
```

**Step 2: Run tests to verify RED**

Run: `php tests/test-footer.php && php tests/test-newsletter-embed.php`

Expected: FAIL on missing navigation, CTA and anchor.

**Step 3: Implement semantic markup**

In `template-parts/lettre.php`, change the opening element to:

```php
<aside id="recevoir-les-pieces" class="lettre" aria-labelledby="lettre-titre">
```

In `footer.php`, fetch `refoulement_liens_pied()`, use the legal entries inside
a named `<nav>`, and render the newsletter entry as a separate third column.
Preserve the existing `pied` menu and widget area below the automatic legal
links so existing installations remain compatible.

Use this structure:

```php
<?php $refoulement_liens_pied = refoulement_liens_pied(); ?>
<div class="pied__interieur">
	<div class="pied__colonne pied__colonne--identite">…</div>
	<div class="pied__colonne pied__colonne--informations">
		<p class="pied__titre"><?php esc_html_e( 'Informations', 'refoulement' ); ?></p>
		<nav aria-label="<?php esc_attr_e( 'Informations légales', 'refoulement' ); ?>">
			<ul class="menu-pied menu-pied--legal">…mentions…confidentialite…</ul>
		</nav>
		…menu et widgets existants…
	</div>
	<div class="pied__colonne pied__colonne--lettre">
		<p class="pied__titre"><?php esc_html_e( 'Recevoir les pièces', 'refoulement' ); ?></p>
		<p class="pied__accroche"><?php esc_html_e( 'Suivre les nouvelles pièces versées au dossier.', 'refoulement' ); ?></p>
		<a class="pied__appel" href="…newsletter…"><?php esc_html_e( 'S’inscrire à la newsletter', 'refoulement' ); ?></a>
	</div>
</div>
```

**Step 4: Implement responsive CSS**

Change `.pied__interieur` to a three-column grid with
`grid-template-columns: minmax(0, 1.35fr) repeat(2, minmax(190px, 1fr))`.
Add `.pied__titre`, `.pied__appel`, `:focus-visible`, a 44px minimum interactive
height, and `scroll-margin-top` on `.lettre`. At 780px, switch to one column.
Do not add new fonts, icons, JavaScript or animation.

**Step 5: Run tests and lint to verify GREEN**

Run:

```bash
php tests/test-footer.php
php tests/test-newsletter-embed.php
php -l footer.php
php -l template-parts/lettre.php
```

Expected: both tests print `OK`; both files report no syntax errors.

**Step 6: Commit**

```bash
git add assets/css/theme.css footer.php template-parts/lettre.php tests/test-footer.php tests/test-newsletter-embed.php
git commit -m "feat(pied): ajouter les liens légaux et la newsletter"
```

## Task 4: Rédiger les contenus légaux déployables

**Files:**
- Create: `docs/content/mentions-legales.html`
- Create: `docs/content/confidentialite.html`
- Modify: `README.md:100-135`

**Step 1: Create the legal notice source**

Create `docs/content/mentions-legales.html` with these sections and facts:

```html
<!-- Vérifié le 28 août 2026. Source de déploiement WordPress, exclue du paquet du thème. -->
<h2>Édition du site</h2>
<p>Le site « Le projet de théâtre », accessible à l’adresse theatre.mehdiguiraud.info, est édité à titre personnel et non professionnel.</p>
<p>Directeur de la publication : Mehdi Guiraud.</p>
<p>Contact : <a href="mailto:fred2baro+dataofficer@gmail.com">fred2baro+dataofficer@gmail.com</a>.</p>
<p>Les éléments permettant l’identification personnelle de l’éditeur ont été communiqués à l’hébergeur conformément à l’article 6 de la loi pour la confiance dans l’économie numérique.</p>

<h2>Hébergement</h2>
<p>Le site est hébergé par OVH SAS, société par actions simplifiée au capital de 50 000 000 €, immatriculée au RCS de Lille Métropole sous le numéro 424 761 419 00045, dont le siège social est situé 2 rue Kellermann, 59100 Roubaix, France. Téléphone : 1007 depuis la France ou +33 9 72 10 10 07 depuis l’étranger.</p>

<h2>Propriété intellectuelle</h2>
<p>Sauf indication contraire, les textes, la structure éditoriale et les créations originales publiés sur ce site sont protégés par le droit de la propriété intellectuelle. Toute reproduction ou réutilisation substantielle nécessite l’autorisation préalable de leur auteur. Les documents, citations, images et marques attribués à des tiers restent soumis aux droits de leurs titulaires respectifs.</p>

<h2>Correction et signalement</h2>
<p>Pour demander la correction d’une information, signaler un contenu ou faire valoir un droit, écrivez à <a href="mailto:fred2baro+dataofficer@gmail.com">fred2baro+dataofficer@gmail.com</a> en indiquant l’adresse de la page concernée et le motif de la demande.</p>

<h2>Responsabilité</h2>
<p>Les contenus sont publiés dans le cadre d’un projet éditorial et artistique. Ils peuvent être corrigés ou complétés. L’éditeur s’efforce de maintenir des informations exactes et des liens accessibles, sans pouvoir garantir l’absence permanente d’erreur ou d’indisponibilité.</p>
```

**Step 2: Create the privacy source**

Create `docs/content/confidentialite.html`. Preserve the substance of page 3
and add the verified inventory using these headings:

```html
<!-- Vérifié le 28 août 2026. Source de déploiement WordPress, exclue du paquet du thème. -->
<p><em>Dernière mise à jour : 28 août 2026.</em></p>
<h2>Responsable du traitement</h2>
<p>Mehdi Guiraud est responsable des traitements décrits sur cette page. Pour toute question ou demande relative à vos données : <a href="mailto:fred2baro+dataofficer@gmail.com">fred2baro+dataofficer@gmail.com</a>.</p>

<h2>Newsletter « Recevoir les pièces »</h2>
<p>Le formulaire permet de recevoir par courrier électronique les actualités du projet et de gérer l’abonnement. Le traitement repose sur votre consentement.</p>
<p>L’adresse électronique est obligatoire. Le prénom et le nom sont facultatifs. Sont également conservés la date, le statut de l’inscription et les éléments nécessaires pour établir la preuve du consentement. L’inscription n’est effective qu’après confirmation du lien envoyé par courrier électronique.</p>
<p>Les données sont transmises directement par la personne qui remplit le formulaire. Elles sont accessibles à Mehdi Guiraud et à Brevo, prestataire chargé des inscriptions et des envois. Brevo indique que les serveurs qui traitent et stockent ses bases de données sont exclusivement situés dans l’Union européenne.</p>
<p>Les données d’abonnement sont conservées tant que vous restez inscrit. Vous pouvez retirer votre consentement à tout moment grâce au lien de désinscription présent dans chaque message. Après désinscription, les éléments strictement nécessaires au respect de votre choix et à la preuve du consentement peuvent être conservés pendant trois ans.</p>

<h2>Commentaires</h2>
<p>Lorsque les commentaires sont ouverts et que vous choisissez d’en publier un, WordPress peut traiter le contenu du commentaire, le nom et l’adresse électronique renseignés, ainsi que l’adresse IP et les informations techniques du navigateur nécessaires à la modération et à la lutte contre les abus. Ces données proviennent du formulaire de commentaire. Elles sont utilisées pour publier et modérer votre contribution, sur la base de votre démarche de publication et de l’intérêt légitime à sécuriser les échanges.</p>
<p>Un commentaire publié reste associé à la page concernée jusqu’à sa suppression ou jusqu’à une demande recevable d’effacement. Les informations nécessaires à la prévention des abus peuvent être conservées plus longtemps lorsqu’un incident le justifie.</p>

<h2>Sécurité et journaux techniques</h2>
<p>L’hébergeur OVH et l’extension de sécurité Wordfence peuvent traiter l’adresse IP, la date et l’heure de la requête, l’adresse demandée et des informations relatives au navigateur. Ces données servent à fournir le site, détecter les tentatives malveillantes et protéger l’administration, sur la base de l’intérêt légitime à assurer la sécurité du service. Le suivi détaillé du trafic en direct de Wordfence est désactivé sur ce site. Les journaux sont conservés pendant une durée limitée compatible avec le diagnostic et la sécurité, selon la configuration des prestataires.</p>

<h2>Cookies</h2>
<p>Aucun outil publicitaire ou de mesure d’audience n’a été observé lors de la dernière vérification. WordPress et ses extensions peuvent utiliser des cookies strictement nécessaires à l’administration, à la sécurité ou, si vous le choisissez, à la mémorisation des informations d’un commentaire. Ces cookies ne sont pas utilisés pour établir un profil publicitaire.</p>

<h2>Destinataires et transferts</h2>
<p>Les données sont accessibles uniquement selon leurs besoins à Mehdi Guiraud et aux prestataires techniques concernés : OVH pour l’hébergement, Brevo pour la newsletter et Wordfence pour la sécurité. Brevo héberge ses bases dans l’Union européenne. Lorsque les services de Wordfence impliquent un transfert hors de l’Espace économique européen, le fournisseur indique recourir aux garanties contractuelles applicables.</p>

<h2>Vos droits</h2>
<p>Vous pouvez demander l’accès, la rectification, l’effacement, la limitation ou la portabilité de vos données, vous opposer à un traitement fondé sur l’intérêt légitime et retirer à tout moment un consentement, sans remettre en cause les opérations antérieures. Écrivez à <a href="mailto:fred2baro+dataofficer@gmail.com">fred2baro+dataofficer@gmail.com</a>. Une réponse vous sera apportée dans les délais prévus par la réglementation.</p>
<p>Si vous estimez que vos droits ne sont pas respectés après avoir pris contact, vous pouvez adresser une réclamation à la <a href="https://www.cnil.fr/fr/plaintes">Commission nationale de l’informatique et des libertés (CNIL)</a>.</p>

<h2>Décision automatisée</h2>
<p>Les traitements décrits ne donnent lieu à aucune décision produisant des effets juridiques fondée exclusivement sur un traitement automatisé.</p>
```

**Step 3: Document administration**

In `README.md`, document the footer setting, the automatic privacy link, the
newsletter anchor, the source files under `docs/content/`, and the need to review
legal content when plugins or data uses change.

**Step 4: Review sources and diff**

Run:

```bash
rg -n "Mehdi Guiraud|OVH SAS|Brevo|Wordfence|CNIL|fred2baro" docs/content README.md
git diff --check
```

Expected: every factual party appears; `git diff --check` exits 0.

**Step 5: Commit**

```bash
git add docs/content README.md
git commit -m "docs: rédiger les contenus légaux du site"
```

## Task 5: Verify and package the complete theme

**Skills:** @superpowers:verification-before-completion

**Files:**
- Modify if needed: `README.md`
- Generated and ignored: `dist/refoulement-1.0.0.zip`

**Step 1: Run all standalone tests**

Run:

```bash
php tests/test-newsletter-embed.php
php tests/test-footer.php
```

Expected: both print `OK`, zero failures.

**Step 2: Lint every PHP file**

Run: `find . -path './.git' -prune -o -name '*.php' -exec php -l {} \;`

Expected: no syntax error.

**Step 3: Check the diff and worktree**

Run: `git diff --check && git status --short --branch && git log -6 --oneline`

Expected: no uncommitted source changes; feature commits visible.

**Step 4: Build and inspect the package**

Run:

```bash
bash bin/build-package.sh
unzip -l dist/refoulement-1.0.0.zip | rg 'inc/(newsletter|footer)\.php|footer\.php|template-parts/lettre\.php|assets/css/theme\.css'
unzip -l dist/refoulement-1.0.0.zip | rg 'tests/|docs/|bin/' && exit 1 || true
```

Expected: the new runtime files exist; tests, docs and bin are absent.

## Task 6: Deploy code and legal content safely

**Skills:** @superpowers:verification-before-completion

**Files deployed:**
- `functions.php`
- `inc/customizer.php`
- `inc/newsletter.php`
- `inc/footer.php`
- `template-parts/lettre.php`
- `footer.php`
- `assets/css/theme.css`
- WordPress pages sourced from `docs/content/*.html`

**Step 1: Back up production**

Use the existing `~/backups/refoulement-2026-08-28/` directory. Copy every
theme file that will be overwritten. Through WordPress bootstrap, write JSON
backups of page 3, the current `theme_mods_refoulement` option and any existing
page whose slug is `mentions-legales`.

Expected: all backup files exist and have non-zero size before continuing.

**Step 2: Deploy the theme files**

Upload only the seven runtime files above. Verify remote `php -l` for every PHP
file and grep for `refoulement_accessibiliser_formulaire_brevo`,
`refoulement_liens_pied` and `recevoir-les-pieces`.

**Step 3: Stage and publish the page content**

Copy `docs/content/mentions-legales.html` and `docs/content/confidentialite.html`
to the backup directory, then run a PHP CLI command after `require 'wp-load.php'`
that:

1. creates or updates the published page `mentions-legales` with title
   « Mentions légales » and the staged HTML;
2. updates page 3 to title « Confidentialité », slug `confidentialite`, and the
   staged privacy HTML;
3. keeps page 3 assigned to `wp_page_for_privacy_policy`;
4. sets `refoulement_mentions_legales_page_id` to the resulting published page ID;
5. verifies both permalinks and titles after `wp_insert_post()`/`wp_update_post()`.

Do not overwrite a page if staging or backup failed. Treat `WP_Error` as a
non-zero exit.

**Step 4: Purge WP Super Cache through WordPress**

Call `wp_cache_clear_cache()` after the page and theme updates. Do not delete a
broad cache directory manually.

**Step 5: Verify public HTML without a cache-buster**

Fetch `https://theatre.mehdiguiraud.info/` and assert:

- HTTP 200;
- one `.lettre__formulaire--brevo` and no `.lettre__iframe`;
- three associated labels for the current Brevo fields;
- `#recevoir-les-pieces` exists;
- « Mentions légales », « Confidentialité » and « S’inscrire à la newsletter »
  exist in the footer;
- both legal pages return HTTP 200;
- no literal `[sibwp_form`, PHP warning or fatal error.

**Step 6: Run browser QA at four widths**

At 375, 768, 1024 and 1440 px, verify:

- no horizontal overflow;
- footer grid reflows without overlap;
- all footer links and fields have visible focus;
- `getByLabel('Email Address*')`, `getByLabel('FIRSTNAME')` and
  `getByLabel('LASTNAME')` resolve exactly one input each;
- the footer newsletter link reaches `#recevoir-les-pieces`;
- no browser console errors.

Do not submit the newsletter form: an end-to-end subscription would create an
external contact and requires a separate explicit test address and confirmation.

**Step 7: Report the evidence and rollback path**

Report tests, lint, package, live HTTP checks, responsive sizes and the exact
backup paths. Rollback consists of restoring the copied theme files, restoring
page 3 and theme mods from JSON, moving the new legal page to draft, and purging
WP Super Cache again.
