<?php
/**
 * Intégration SEO et validation pour les outils webmestre (Google Search Console).
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

/**
 * Répond au fichier de vérification Google Search Console si intercepté par WordPress.
 */
function refoulement_google_search_console_file() {
	if ( isset( $_SERVER['REQUEST_URI'] ) && false !== strpos( $_SERVER['REQUEST_URI'], 'google8a0d27bc313a0a0e.html' ) ) {
		status_header( 200 );
		header( 'Content-Type: text/html; charset=utf-8' );
		echo "google-site-verification: google8a0d27bc313a0a0e.html\n";
		exit;
	}
}
add_action( 'init', 'refoulement_google_search_console_file' );

/**
 * Balise meta de vérification pour Google Search Console.
 */
function refoulement_google_search_console_meta() {
	echo '<meta name="google-site-verification" content="8a0d27bc313a0a0e">' . "\n";
}
add_action( 'wp_head', 'refoulement_google_search_console_meta', 1 );
