<?php
/**
 * Test unitaire : vérification Google Search Console.
 *
 * Exécution : php tests/test-seo.php
 */

define( 'ABSPATH', __DIR__ . '/' );

if ( ! function_exists( 'add_action' ) ) {
	function add_action() {}
}

if ( ! function_exists( 'status_header' ) ) {
	function status_header( $code ) {}
}

require_once __DIR__ . '/../inc/seo.php';

// Test 1 : Balise meta dans l'en-tête
ob_start();
refoulement_google_search_console_meta();
$meta_output = ob_get_clean();

if ( ! str_contains( $meta_output, 'google-site-verification' ) || ! str_contains( $meta_output, '8a0d27bc313a0a0e' ) ) {
	echo "FAIL - balise meta de vérification absente ou incorrecte.\n";
	exit( 1 );
}

// Test 2 : Fichier HTML racine existe
$root_file = dirname( __DIR__ ) . '/google8a0d27bc313a0a0e.html';
if ( ! file_exists( $root_file ) ) {
	echo "FAIL - fichier racine google8a0d27bc313a0a0e.html introuvable.\n";
	exit( 1 );
}

$content = trim( file_get_contents( $root_file ) );
if ( 'google-site-verification: google8a0d27bc313a0a0e.html' !== $content ) {
	echo "FAIL - contenu du fichier racine non conforme : $content\n";
	exit( 1 );
}

echo "OK — SEO / Google Search Console validé.\n";
