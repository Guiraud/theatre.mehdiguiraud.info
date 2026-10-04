<?php
/**
 * Tests autonomes du carrousel vidéo de l'accueil.
 *
 * Exécution : php tests/test-videos.php
 * Avec --rendu, affiche le HTML du gabarit au lieu de lancer les tests.
 */

define( 'ABSPATH', __DIR__ . '/' );

$GLOBALS['refoulement_theme_mods'] = array();

function __( $text ) {
	return $text;
}

function esc_html__( $text ) {
	return esc_html( $text );
}

function esc_attr__( $text ) {
	return esc_attr( $text );
}

function esc_attr_e( $text ) {
	echo esc_attr( $text );
}

function add_action() {
}

function get_theme_mod( $key, $default = '' ) {
	return array_key_exists( $key, $GLOBALS['refoulement_theme_mods'] )
		? $GLOBALS['refoulement_theme_mods'][ $key ]
		: $default;
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

require dirname( __DIR__ ) . '/inc/customizer.php';

function rendu( $mods ) {
	$GLOBALS['refoulement_theme_mods'] = $mods;
	ob_start();
	include dirname( __DIR__ ) . '/template-parts/videos.php';
	return ob_get_clean();
}

if ( in_array( '--rendu', $argv, true ) ) {
	echo rendu( array() );
	exit( 0 );
}

$echecs = 0;

function verifier( $condition, $message ) {
	global $echecs;

	if ( $condition ) {
		echo "ok   - {$message}\n";
		return;
	}

	$echecs++;
	echo "ÉCHEC - {$message}\n";
}

$defaut = refoulement_videos();
verifier( 7 === count( $defaut ), 'la liste par défaut compte sept vidéos' );
verifier( 'qBjk-VDbPoI' === $defaut[0]['id'] && 'paysage' === $defaut[0]['format'], 'un épisode watch?v= est en paysage' );
verifier( 'portrait' === $defaut[2]['format'], 'un short est en portrait' );

$GLOBALS['refoulement_theme_mods'] = array(
	'refoulement_videos_liste' => implode( "\n", array(
		'https://youtu.be/NTRPH8KPHko|Court',
		'https://www.youtube.com/watch?feature=share&v=0tZJnGzqT3A',
		'https://vimeo.com/123456|Ignorée',
		'javascript:alert(1)//youtube.com/shorts/aaaaaaaaaaa|Piège',
		'',
		'   ',
	) ),
);
$liste = refoulement_videos();
verifier( 2 === count( $liste ), 'les adresses non YouTube et les lignes vides sont ignorées' );
verifier( 'https://www.youtube.com/watch?v=NTRPH8KPHko' === $liste[0]['url'], 'youtu.be est réécrit en adresse canonique' );
verifier( '0tZJnGzqT3A' === $liste[1]['id'] && 'Vidéo' === $liste[1]['titre'], 'v= après un autre paramètre, titre de repli' );

verifier( '' === trim( rendu( array( 'refoulement_videos_liste' => '' ) ) ), 'liste vide : aucun rendu' );

$html = rendu( array( 'refoulement_videos_liste' => 'https://youtu.be/NTRPH8KPHko|"><script>x</script>' ) );
verifier( ! str_contains( $html, '<script>' ), 'le titre est échappé' );
verifier( str_contains( $html, 'data-video-id="NTRPH8KPHko"' ), 'le lanceur porte l’identifiant' );

exit( $echecs ? 1 : 0 );
