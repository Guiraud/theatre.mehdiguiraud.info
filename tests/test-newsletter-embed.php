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

require dirname( __DIR__ ) . '/inc/customizer.php';

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

function render_newsletter( $mods ) {
	$GLOBALS['refoulement_theme_mods'] = $mods;
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

if ( $failures ) {
	fwrite( STDERR, "ÉCHEC\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "OK — intégration Brevo validée.\n";
