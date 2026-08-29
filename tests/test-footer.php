<?php
/**
 * Tests autonomes de la résolution des liens du pied de page.
 *
 * Exécution : php tests/test-footer.php
 */

define( 'ABSPATH', __DIR__ . '/' );

$GLOBALS['refoulement_theme_mods'] = array();
$GLOBALS['refoulement_pages']      = array();

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

function sanitize_text_field( $value ) {
	return trim( (string) $value );
}

function absint( $value ) {
	return abs( (int) $value );
}

function home_url( $chemin = '/' ) {
	return 'https://example.test' . $chemin;
}

/**
 * Statut d'une page factice ; `false` quand la page n'existe pas, comme WordPress.
 *
 * @param int $id Identifiant de page.
 * @return string|false
 */
function get_post_status( $id ) {
	return $GLOBALS['refoulement_pages'][ $id ]['statut'] ?? false;
}

function get_permalink( $id ) {
	return isset( $GLOBALS['refoulement_pages'][ $id ] )
		? 'https://example.test/' . $GLOBALS['refoulement_pages'][ $id ]['slug'] . '/'
		: false;
}

function get_privacy_policy_url() {
	$id = $GLOBALS['refoulement_privacy_page'] ?? 0;

	if ( ! $id || 'publish' !== ( $GLOBALS['refoulement_pages'][ $id ]['statut'] ?? '' ) ) {
		return '';
	}

	return get_permalink( $id );
}

require_once __DIR__ . '/../inc/customizer.php';
require_once __DIR__ . '/../inc/footer.php';

$failures = array();

function expect_same( $expected, $actual, $message ) {
	global $failures;

	if ( $expected !== $actual ) {
		$failures[] = $message . ' — attendu ' . var_export( $expected, true ) . ', reçu ' . var_export( $actual, true );
	}
}

$GLOBALS['refoulement_pages'] = array(
	3  => array(
		'statut' => 'publish',
		'slug'   => 'confidentialite',
	),
	42 => array(
		'statut' => 'publish',
		'slug'   => 'mentions-legales',
	),
	43 => array(
		'statut' => 'draft',
		'slug'   => 'mentions-legales-brouillon',
	),
);

$GLOBALS['refoulement_privacy_page'] = 3;

$GLOBALS['refoulement_theme_mods']['refoulement_mentions_legales_page_id'] = 42;

$liens = refoulement_liens_pied();

expect_same( 'https://example.test/mentions-legales/', $liens['mentions']['url'] ?? null, 'La page publiée doit être liée.' );
expect_same( 'Mentions légales', $liens['mentions']['label'] ?? null, 'Le lien légal doit porter son libellé traduit.' );
expect_same( 'https://example.test/confidentialite/', $liens['confidentialite']['url'] ?? null, 'La politique WordPress doit être liée.' );
expect_same( 'https://example.test/#recevoir-les-pieces', $liens['newsletter']['url'] ?? null, 'La newsletter doit viser l’ancre de l’accueil.' );

$GLOBALS['refoulement_theme_mods']['refoulement_mentions_legales_page_id'] = 43;
$liens = refoulement_liens_pied();

expect_same( false, isset( $liens['mentions'] ), 'Une page en brouillon ne doit pas produire de lien.' );

$GLOBALS['refoulement_theme_mods']['refoulement_mentions_legales_page_id'] = 999;
$liens = refoulement_liens_pied();

expect_same( false, isset( $liens['mentions'] ), 'Une page supprimée ne doit pas produire de lien mort.' );

unset( $GLOBALS['refoulement_theme_mods']['refoulement_mentions_legales_page_id'] );
$liens = refoulement_liens_pied();

expect_same( false, isset( $liens['mentions'] ), 'Sans page désignée, aucun lien de mentions légales n’est rendu.' );
expect_same( true, isset( $liens['newsletter'] ), 'La newsletter reste toujours accessible depuis le pied de page.' );

$GLOBALS['refoulement_privacy_page'] = 0;
$liens = refoulement_liens_pied();

expect_same( false, isset( $liens['confidentialite'] ), 'Sans politique de confidentialité définie, aucun lien n’est rendu.' );

$GLOBALS['refoulement_privacy_page'] = 43;
$liens = refoulement_liens_pied();

expect_same( false, isset( $liens['confidentialite'] ), 'Une politique de confidentialité non publiée ne doit pas être liée.' );

if ( $failures ) {
	fwrite( STDERR, "ÉCHEC\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "OK — liens du pied de page validés.\n";
