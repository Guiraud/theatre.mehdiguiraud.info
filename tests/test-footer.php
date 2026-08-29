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

function esc_html( $value ) {
	return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
}

function esc_attr( $value ) {
	return esc_html( $value );
}

function esc_url( $value ) {
	return esc_attr( $value );
}

function esc_html__( $texte ) {
	return $texte;
}

function esc_html_e( $texte ) {
	echo esc_html( $texte );
}

function esc_attr_e( $texte ) {
	echo esc_attr( $texte );
}

function get_bloginfo( $champ ) {
	return $GLOBALS['refoulement_bloginfo'][ $champ ] ?? '';
}

function wp_date( $format ) {
	return gmdate( $format );
}

function refoulement_numero_dossier() {
	return 'Dossier n° 1897';
}

function refoulement_mention_edition() {
	return 'édition de test';
}

function has_nav_menu() {
	return false;
}

function wp_nav_menu() {
}

function is_active_sidebar() {
	return false;
}

function dynamic_sidebar() {
}

function wp_footer() {
}

require_once __DIR__ . '/../inc/customizer.php';
require_once __DIR__ . '/../inc/footer.php';

/**
 * Rend le pied de page complet et retourne son balisage.
 *
 * @return string
 */
function render_footer() {
	ob_start();
	include __DIR__ . '/../footer.php';
	return (string) ob_get_clean();
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

// --- Balisage du pied de page --------------------------------------------

$GLOBALS['refoulement_bloginfo'] = array(
	'name'        => 'Le Refoulement de Freud',
	'description' => 'Enquête dramaturgique',
);

$GLOBALS['refoulement_privacy_page'] = 3;
$GLOBALS['refoulement_theme_mods']['refoulement_mentions_legales_page_id'] = 42;

$pied = render_footer();

expect_contains( 'class="pied__interieur"', $pied, 'Le pied doit garder son conteneur.' );
expect_contains( 'aria-label="Informations légales"', $pied, 'Les liens légaux doivent former une navigation nommée.' );
expect_contains( '>Mentions légales</a>', $pied, 'Le lien vers les mentions doit être rendu.' );
expect_contains( '>Confidentialité</a>', $pied, 'Le lien de confidentialité doit être rendu.' );
expect_contains( 'href="https://example.test/mentions-legales/"', $pied, 'Le lien des mentions doit pointer vers la page publiée.' );
expect_contains( 'class="pied__appel"', $pied, 'La newsletter doit être un appel à l’action distinct.' );
expect_contains( 'href="https://example.test/#recevoir-les-pieces"', $pied, 'L’appel doit fonctionner hors de l’accueil.' );

$GLOBALS['refoulement_theme_mods']['refoulement_mentions_legales_page_id'] = 43;
$GLOBALS['refoulement_privacy_page'] = 0;

$pied_sans_pages = render_footer();

expect_not_contains( '>Mentions légales</a>', $pied_sans_pages, 'Une page en brouillon ne doit pas produire de lien dans le pied.' );
expect_not_contains( '>Confidentialité</a>', $pied_sans_pages, 'Sans politique publiée, aucun lien de confidentialité n’est rendu.' );
expect_not_contains( 'aria-label="Informations légales"', $pied_sans_pages, 'Une navigation légale vide ne doit pas être rendue.' );
expect_contains( 'class="pied__appel"', $pied_sans_pages, 'L’appel newsletter reste rendu sans pages légales.' );
expect_not_contains( 'pied__colonne--informations', $pied_sans_pages, 'Sans lien légal, sans menu et sans widget, la colonne « Informations » ne doit pas laisser un titre seul.' );
expect_not_contains( '>Informations<', $pied_sans_pages, 'Le titre « Informations » ne doit pas être rendu au-dessus du vide.' );

if ( $failures ) {
	fwrite( STDERR, "ÉCHEC\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "OK — liens du pied de page validés.\n";
