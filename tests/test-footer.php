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

function get_option( $cle, $defaut = false ) {
	if ( 'wp_page_for_privacy_policy' === $cle ) {
		return $GLOBALS['refoulement_privacy_page'] ?? 0;
	}

	return $defaut;
}

function is_page( $page = 0 ) {
	$courante = $GLOBALS['refoulement_page_courante'] ?? 0;

	return $courante && (int) $page === (int) $courante;
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

function refoulement_manchette() {
	echo '<p class="bandeau__manchette">Le Refoulement</p>';
}

function get_post_type_archive_link( $type ) {
	return 'https://example.test/' . $type . 's/';
}

function wp_kses_post( $valeur ) {
	return $valeur;
}

$GLOBALS['refoulement_pages_listees'] = array();

function wp_list_pages( $arguments = array() ) {
	$GLOBALS['refoulement_pages_listees'] = $arguments;
	$exclues = array_filter( array_map( 'intval', explode( ',', (string) ( $arguments['exclude'] ?? '' ) ) ) );
	$sortie  = '';

	foreach ( $GLOBALS['refoulement_pages'] as $id => $page ) {
		if ( 'publish' !== $page['statut'] || in_array( (int) $id, $exclues, true ) ) {
			continue;
		}

		$sortie .= '<li class="page_item page-item-' . $id . '"><a href="' . get_permalink( $id ) . '">' . $page['slug'] . '</a></li>';
	}

	return $sortie;
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

/**
 * Rend le bandeau de navigation et retourne son balisage.
 *
 * @return string
 */
function render_bandeau() {
	ob_start();
	include __DIR__ . '/../template-parts/bandeau-nav.php';
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
	17 => array(
		'statut' => 'publish',
		'slug'   => 'a-propos',
	),
	60 => array(
		'statut' => 'publish',
		'slug'   => 'recevoir-les-pieces',
	),
	61 => array(
		'statut' => 'draft',
		'slug'   => 'newsletter-brouillon',
	),
);

$GLOBALS['refoulement_privacy_page'] = 3;

$GLOBALS['refoulement_theme_mods']['refoulement_mentions_legales_page_id'] = 42;
$GLOBALS['refoulement_theme_mods']['refoulement_lettre_page_id']          = 60;

$liens = refoulement_liens_pied();

expect_same( 'https://example.test/mentions-legales/', $liens['mentions']['url'] ?? null, 'La page publiée doit être liée.' );
expect_same( 'Mentions légales', $liens['mentions']['label'] ?? null, 'Le lien légal doit porter son libellé traduit.' );
expect_same( 'https://example.test/confidentialite/', $liens['confidentialite']['url'] ?? null, 'La politique WordPress doit être liée.' );
expect_same( 'https://example.test/recevoir-les-pieces/', $liens['newsletter']['url'] ?? null, 'La newsletter doit viser sa page dédiée.' );

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

// --- Destination du lien newsletter ---------------------------------------

$GLOBALS['refoulement_theme_mods']['refoulement_mentions_legales_page_id'] = 42;
$GLOBALS['refoulement_privacy_page'] = 3;

// 1. Une page dédiée publiée l'emporte sur tout le reste.
$GLOBALS['refoulement_theme_mods']['refoulement_lettre_page_id']    = 60;
$GLOBALS['refoulement_theme_mods']['refoulement_lettre_brevo_url']  = 'https://bd1602f2.sibforms.com/v2/serve/jeton==';
$liens = refoulement_liens_pied();

expect_same( 'https://example.test/recevoir-les-pieces/', $liens['newsletter']['url'] ?? null, 'Une page dédiée publiée doit devenir la destination du lien newsletter.' );

// 2. Sans page dédiée, la page hébergée par Brevo prend le relais.
$GLOBALS['refoulement_theme_mods']['refoulement_lettre_page_id'] = 0;
$liens = refoulement_liens_pied();

expect_same( 'https://bd1602f2.sibforms.com/v2/serve/jeton==', $liens['newsletter']['url'] ?? null, 'Sans page dédiée, le formulaire hébergé par Brevo doit servir de destination.' );

// 3. Une page dédiée en brouillon ne doit pas produire de lien mort.
$GLOBALS['refoulement_theme_mods']['refoulement_lettre_page_id'] = 61;
$liens = refoulement_liens_pied();

expect_same( 'https://bd1602f2.sibforms.com/v2/serve/jeton==', $liens['newsletter']['url'] ?? null, 'Une page dédiée en brouillon doit être ignorée au profit du repli.' );

// 4. Sans page ni adresse Brevo, il n'existe aucune destination : pas de lien.
$GLOBALS['refoulement_theme_mods']['refoulement_lettre_page_id']   = 0;
$GLOBALS['refoulement_theme_mods']['refoulement_lettre_brevo_url'] = '';
$liens = refoulement_liens_pied();

expect_same( false, isset( $liens['newsletter'] ), 'Sans page ni formulaire hébergé, aucun appel ne doit être rendu.' );

$pied_sans_destination = render_footer();

expect_not_contains( 'pied__colonne--lettre', $pied_sans_destination, 'Sans destination, la colonne newsletter ne doit pas être rendue.' );
expect_not_contains( 'href=""', $pied_sans_destination, 'Aucun lien vide ne doit subsister.' );

// 5. Sur la page dédiée elle-même, l'appel ne doit pas pointer vers la page courante.
$GLOBALS['refoulement_theme_mods']['refoulement_lettre_page_id'] = 60;
$GLOBALS['refoulement_page_courante'] = 60;
$liens = refoulement_liens_pied();

expect_same( false, isset( $liens['newsletter'] ), 'Sur la page dédiée, l’appel du pied ne doit pas renvoyer à elle-même.' );

// 6. Le pied ne doit pas rendre de colonne ni de lien vide sur cette page.
$pied_page_lettre = render_footer();

expect_not_contains( 'pied__appel', $pied_page_lettre, 'Aucun appel ne doit être rendu sur la page dédiée.' );
expect_not_contains( 'pied__colonne--lettre', $pied_page_lettre, 'La colonne newsletter ne doit pas rester vide sur la page dédiée.' );
expect_not_contains( 'href=""', $pied_page_lettre, 'Le pied ne doit jamais rendre de lien vide.' );

$GLOBALS['refoulement_page_courante'] = 0;
$GLOBALS['refoulement_theme_mods']['refoulement_lettre_page_id'] = 0;

// --- Balisage du pied de page --------------------------------------------

$GLOBALS['refoulement_bloginfo'] = array(
	'name'        => 'Le Refoulement de Freud',
	'description' => 'Enquête dramaturgique',
);

$GLOBALS['refoulement_privacy_page'] = 3;
$GLOBALS['refoulement_theme_mods']['refoulement_mentions_legales_page_id'] = 42;
$GLOBALS['refoulement_theme_mods']['refoulement_lettre_page_id']          = 60;

$pied = render_footer();

expect_contains( 'class="pied__interieur"', $pied, 'Le pied doit garder son conteneur.' );
expect_contains( 'aria-label="Informations légales"', $pied, 'Les liens légaux doivent former une navigation nommée.' );
expect_contains( '>Mentions légales</a>', $pied, 'Le lien vers les mentions doit être rendu.' );
expect_contains( '>Confidentialité</a>', $pied, 'Le lien de confidentialité doit être rendu.' );
expect_contains( 'href="https://example.test/mentions-legales/"', $pied, 'Le lien des mentions doit pointer vers la page publiée.' );
expect_contains( 'class="pied__appel"', $pied, 'La newsletter doit être un appel à l’action distinct.' );
expect_contains( 'href="https://example.test/recevoir-les-pieces/"', $pied, 'L’appel doit mener à la page dédiée depuis n’importe quelle page.' );

$GLOBALS['refoulement_theme_mods']['refoulement_mentions_legales_page_id'] = 43;
$GLOBALS['refoulement_privacy_page'] = 0;

$pied_sans_pages = render_footer();

expect_not_contains( '>Mentions légales</a>', $pied_sans_pages, 'Une page en brouillon ne doit pas produire de lien dans le pied.' );
expect_not_contains( '>Confidentialité</a>', $pied_sans_pages, 'Sans politique publiée, aucun lien de confidentialité n’est rendu.' );
expect_not_contains( 'aria-label="Informations légales"', $pied_sans_pages, 'Une navigation légale vide ne doit pas être rendue.' );
expect_contains( 'class="pied__appel"', $pied_sans_pages, 'L’appel newsletter reste rendu sans pages légales.' );
expect_not_contains( 'pied__colonne--informations', $pied_sans_pages, 'Sans lien légal, sans menu et sans widget, la colonne « Informations » ne doit pas laisser un titre seul.' );
expect_not_contains( '>Informations<', $pied_sans_pages, 'Le titre « Informations » ne doit pas être rendu au-dessus du vide.' );

// --- Navigation du haut : les pages légales restent au pied ----------------

$GLOBALS['refoulement_privacy_page'] = 3;
$GLOBALS['refoulement_theme_mods']['refoulement_mentions_legales_page_id'] = 42;

expect_same( array( 42, 3 ), refoulement_pages_legales(), 'Les deux pages légales publiées doivent être identifiées.' );

$bandeau = render_bandeau();

expect_not_contains( 'page-item-42', $bandeau, 'Les mentions légales ne doivent pas apparaître dans la navigation du haut.' );
expect_not_contains( 'page-item-3', $bandeau, 'La confidentialité ne doit pas apparaître dans la navigation du haut.' );
expect_contains( 'page-item-17', $bandeau, 'Les pages éditoriales doivent rester dans la navigation du haut.' );

$GLOBALS['refoulement_theme_mods']['refoulement_mentions_legales_page_id'] = 43;
$GLOBALS['refoulement_privacy_page'] = 0;

expect_same( array(), refoulement_pages_legales(), 'Sans page légale publiée, rien n’est exclu de la navigation.' );

if ( $failures ) {
	fwrite( STDERR, "ÉCHEC\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "OK — liens du pied de page validés.\n";
