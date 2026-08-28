<?php
/**
 * Tests autonomes de l'intégration Brevo.
 *
 * Exécution : php tests/test-newsletter-embed.php
 */

define( 'ABSPATH', __DIR__ . '/' );

$GLOBALS['refoulement_theme_mods'] = array();

function __( $text ) {
	return $text;
}

function add_action() {
}

function get_theme_mod( $key, $default = '' ) {
	return array_key_exists( $key, $GLOBALS['refoulement_theme_mods'] )
		? $GLOBALS['refoulement_theme_mods'][ $key ]
		: $default;
}

function esc_url_raw( $url, $protocols = null ) {
	$url    = filter_var( trim( (string) $url ), FILTER_SANITIZE_URL );
	$scheme = parse_url( $url, PHP_URL_SCHEME );

	if ( $protocols && ! in_array( $scheme, $protocols, true ) ) {
		return '';
	}

	return $url;
}

function wp_parse_url( $url, $component = -1 ) {
	return parse_url( $url, $component );
}

function apply_filters( $hook, $value ) {
	return $value;
}

function esc_html( $value ) {
	return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
}

function esc_attr( $value ) {
	return esc_html( $value );
}

function esc_url( $value ) {
	return esc_attr( $value );
}

function esc_html_e( $value ) {
	echo esc_html( $value );
}

function wp_kses_post( $value ) {
	return $value;
}

$GLOBALS['refoulement_shortcodes'] = array();

function shortcode_exists( $tag ) {
	return array_key_exists( $tag, $GLOBALS['refoulement_shortcodes'] );
}

function do_shortcode( $contenu ) {
	foreach ( $GLOBALS['refoulement_shortcodes'] as $tag => $sortie ) {
		if ( str_starts_with( trim( $contenu ), '[' . $tag ) ) {
			return $sortie;
		}
	}

	return $contenu;
}

require dirname( __DIR__ ) . '/inc/customizer.php';

if ( file_exists( dirname( __DIR__ ) . '/inc/newsletter.php' ) ) {
	require dirname( __DIR__ ) . '/inc/newsletter.php';
}

$failures = array();

function expect_same( $expected, $actual, $message ) {
	global $failures;

	if ( $expected !== $actual ) {
		$failures[] = $message . ' — attendu ' . var_export( $expected, true ) . ', reçu ' . var_export( $actual, true );
	}
}

function expect_contains( $needle, $haystack, $message ) {
	global $failures;

	if ( false === strpos( $haystack, $needle ) ) {
		$failures[] = $message . ' — fragment absent : ' . $needle;
	}
}

function expect_not_contains( $needle, $haystack, $message ) {
	global $failures;

	if ( false !== strpos( $haystack, $needle ) ) {
		$failures[] = $message . ' — fragment inattendu : ' . $needle;
	}
}

function render_newsletter( $mods, $shortcodes = array() ) {
	$GLOBALS['refoulement_theme_mods'] = $mods;
	$GLOBALS['refoulement_shortcodes'] = $shortcodes;
	ob_start();
	include dirname( __DIR__ ) . '/template-parts/lettre.php';
	return ob_get_clean();
}

$valid_url = 'https://bd1602f2.sibforms.com/v2/serve/public-token==';

if ( ! function_exists( 'refoulement_sanitize_brevo_embed_url' ) ) {
	$failures[] = 'La fonction refoulement_sanitize_brevo_embed_url doit exister.';
} else {
	expect_same( $valid_url, refoulement_sanitize_brevo_embed_url( $valid_url ), 'Une URL Brevo HTTPS valide doit être conservée.' );
	expect_same( '', refoulement_sanitize_brevo_embed_url( str_replace( 'https://', 'http://', $valid_url ) ), 'HTTP doit être refusé.' );
	expect_same( '', refoulement_sanitize_brevo_embed_url( 'https://example.com/v2/serve/public-token' ), 'Un autre domaine doit être refusé.' );
	expect_same( '', refoulement_sanitize_brevo_embed_url( 'https://bd1602f2.sibforms.com/not-a-form' ), 'Un autre chemin doit être refusé.' );
}

$markup = render_newsletter( array(
	'refoulement_lettre_brevo_url' => $valid_url,
) );

expect_contains( 'class="lettre__iframe no-lazyload"', $markup, 'Le formulaire Brevo doit être rendu dans une iframe dédiée, exclue du chargement différé de Smush.' );
expect_contains( 'src="' . $valid_url . '"', $markup, 'L’iframe doit utiliser l’URL Brevo validée.' );
expect_contains( 'title="Formulaire d’inscription à la newsletter Recevoir les pièces"', $markup, 'L’iframe doit avoir un titre accessible.' );
expect_contains( 'loading="lazy"', $markup, 'L’iframe doit être chargée paresseusement.' );
expect_contains( 'width="540"', $markup, 'Une largeur intrinsèque doit réserver la mise en page.' );
expect_contains( 'height="700"', $markup, 'Une hauteur intrinsèque doit éviter un formulaire à défilement interne sur mobile.' );

$invalid_markup = render_newsletter( array(
	'refoulement_lettre_brevo_url' => 'https://example.com/v2/serve/public-token',
) );

expect_same( '', trim( $invalid_markup ), 'Une URL d’intégration non Brevo ne doit rien rendre.' );

// --- Formulaire natif via le plugin Brevo (shortcode) ---------------------

$forme_html = '<form id="sib_signup_form_1" class="sib_signup_form"><input type="hidden" name="sib_form_action" value="subscribe_form_submit"><p>Email Address*<input type="email" name="email" required></p><p>FIRSTNAME<input type="text" name="FIRSTNAME"></p><p>LASTNAME<input type="text" name="LASTNAME"></p><input type="submit" value="Je m’inscris"><style>.sib_signup_form{display:block}</style></form>';

if ( ! function_exists( 'refoulement_accessibiliser_formulaire_brevo' ) ) {
	$failures[] = 'La fonction refoulement_accessibiliser_formulaire_brevo doit exister.';
} else {
	$forme_accessible = refoulement_accessibiliser_formulaire_brevo( $forme_html );

	expect_contains( '<label for="refoulement-brevo-1-email">Email Address*</label>', $forme_accessible, 'Le texte du champ courriel doit redevenir une étiquette associée.' );
	expect_contains( 'id="refoulement-brevo-1-email"', $forme_accessible, 'Le champ courriel doit recevoir un identifiant stable.' );
	expect_contains( '<label for="refoulement-brevo-1-firstname">FIRSTNAME</label>', $forme_accessible, 'Le prénom doit recevoir une étiquette associée.' );
	expect_contains( 'id="refoulement-brevo-1-firstname"', $forme_accessible, 'Le champ prénom doit recevoir un identifiant stable.' );
	expect_contains( '<label for="refoulement-brevo-1-lastname">LASTNAME</label>', $forme_accessible, 'Le nom doit recevoir une étiquette associée.' );
	expect_contains( 'id="refoulement-brevo-1-lastname"', $forme_accessible, 'Le champ nom doit recevoir un identifiant stable.' );
	expect_contains( '<input type="hidden" name="sib_form_action" value="subscribe_form_submit">', $forme_accessible, 'Le champ masqué du plugin doit être préservé.' );
	expect_contains( '<input type="submit" value="Je m’inscris">', $forme_accessible, 'Le bouton d’envoi ne doit pas être transformé.' );
	expect_contains( '<style>.sib_signup_form{display:block}</style>', $forme_accessible, 'Les styles propres au formulaire doivent être préservés.' );
	expect_same( $forme_accessible, refoulement_accessibiliser_formulaire_brevo( $forme_accessible ), 'La transformation doit être idempotente.' );

	$forme_deja_accessible = '<form id="sib_signup_form_2"><p><label for="courriel-existant">Votre courriel</label><input id="courriel-existant" type="email" name="email"></p><p><input type="text" name="FIRSTNAME" aria-label="Prénom"></p><p><span id="nom-description">Nom</span><input type="text" name="LASTNAME" aria-labelledby="nom-description"></p></form>';
	expect_same( $forme_deja_accessible, refoulement_accessibiliser_formulaire_brevo( $forme_deja_accessible ), 'Un formulaire déjà accessible doit rester strictement inchangé.' );

	$forme_aria_vide = '<form id="sib_signup_form_4"><p><span id="etiquette-vide"></span>Ville<input type="text" name="CITY" aria-labelledby="etiquette-vide"></p></form>';
	$forme_aria_reparee = refoulement_accessibiliser_formulaire_brevo( $forme_aria_vide );
	expect_contains( '<label for="refoulement-brevo-4-city">Ville</label>', $forme_aria_reparee, 'Une référence ARIA vide ne doit pas empêcher la restauration d’une vraie étiquette.' );
	expect_not_contains( 'aria-labelledby="etiquette-vide"', $forme_aria_reparee, 'Une référence ARIA sans nom accessible doit être retirée du champ réparé.' );

	$forme_noms_dupliques = '<form id="sib_signup_form_7"><p>Code postal<input type="text" name="contact[postal-code]"></p><p>Confirmation<input type="text" name="contact[postal-code]"></p><p>Consentement<input type="checkbox" name="consent"></p></form>';
	$forme_ids_uniques = refoulement_accessibiliser_formulaire_brevo( $forme_noms_dupliques );
	expect_contains( 'id="refoulement-brevo-7-contact-postal-code"', $forme_ids_uniques, 'Les caractères spéciaux du nom doivent produire un identifiant HTML stable.' );
	expect_contains( 'id="refoulement-brevo-7-contact-postal-code-2"', $forme_ids_uniques, 'Deux champs de même nom doivent recevoir des identifiants distincts.' );
	expect_not_contains( 'name="consent" id=', $forme_ids_uniques, 'Une case à cocher ne doit pas être transformée comme un champ de texte.' );
}

if ( ! function_exists( 'refoulement_sanitize_brevo_form_id' ) ) {
	$failures[] = 'La fonction refoulement_sanitize_brevo_form_id doit exister.';
} else {
	expect_same( 12, refoulement_sanitize_brevo_form_id( '12' ), 'Un identifiant numérique doit être conservé.' );
	expect_same( 0, refoulement_sanitize_brevo_form_id( '0' ), 'Zéro ne désigne aucun formulaire.' );
	expect_same( 0, refoulement_sanitize_brevo_form_id( '-3' ), 'Un identifiant négatif doit être refusé.' );
	expect_same( 0, refoulement_sanitize_brevo_form_id( 'abc' ), 'Un identifiant non numérique doit être refusé.' );
	expect_same( 0, refoulement_sanitize_brevo_form_id( '' ), 'Une valeur vide doit être refusée.' );
}

$markup_shortcode = render_newsletter(
	array(
		'refoulement_lettre_brevo_form_id' => 3,
		'refoulement_lettre_brevo_url'     => $valid_url,
	),
	array( 'sibwp_form' => $forme_html )
);

expect_contains( 'lettre__formulaire--brevo', $markup_shortcode, 'Le formulaire natif doit porter sa classe de mise en page.' );
expect_contains( 'lettre__interieur lettre__interieur--natif', $markup_shortcode, 'Le conteneur doit passer en colonne : le formulaire natif est un bloc vertical, pas la paire champ + bouton en ligne.' );
expect_contains( 'sib_signup_form', $markup_shortcode, 'Le balisage du plugin Brevo doit être rendu tel quel.' );
expect_contains( '<label for="refoulement-brevo-1-email">Email Address*</label>', $markup_shortcode, 'Le formulaire Brevo rendu par le thème doit retrouver ses étiquettes accessibles.' );
expect_contains( '<h2 id="lettre-titre" class="lettre__titre"', $markup_shortcode, 'Le titre reste visible : le formulaire natif ne porte pas le sien.' );

if ( false !== strpos( $markup_shortcode, '<iframe' ) ) {
	$failures[] = 'Le formulaire natif doit avoir la priorité sur l’iframe.';
}

$markup_sans_plugin = render_newsletter(
	array(
		'refoulement_lettre_brevo_form_id' => 3,
		'refoulement_lettre_brevo_url'     => $valid_url,
	),
	array()
);

expect_contains( 'class="lettre__iframe no-lazyload"', $markup_sans_plugin, 'Sans clé API le shortcode n’existe pas : repli sur l’iframe.' );

$markup_forme_absente = render_newsletter(
	array(
		'refoulement_lettre_brevo_form_id' => 99,
		'refoulement_lettre_brevo_url'     => $valid_url,
	),
	array( 'sibwp_form' => '' )
);

expect_contains( 'class="lettre__iframe no-lazyload"', $markup_forme_absente, 'Un identifiant de formulaire inconnu rend une chaîne vide : repli sur l’iframe.' );

if ( $failures ) {
	fwrite( STDERR, "ÉCHEC\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "OK — intégration Brevo validée.\n";
