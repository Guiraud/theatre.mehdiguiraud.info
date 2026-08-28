<?php
/**
 * Réglages généraux du thème.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

/**
 * Déclare les fonctionnalités supportées.
 */
function refoulement_setup() {
	load_theme_textdomain( 'refoulement', REFOULEMENT_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'custom-logo', array(
		'height'      => 48,
		'width'       => 320,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
		'navigation-widgets',
	) );

	add_editor_style( array( 'assets/css/fonts.css', 'assets/css/editor.css' ) );

	register_nav_menus( array(
		'principal' => __( 'Navigation principale', 'refoulement' ),
		'pied'      => __( 'Pied de page', 'refoulement' ),
	) );

	// Format de découpe des documents versés au dossier (pièces jointes).
	add_image_size( 'refoulement-piece', 900, 640, true );
	add_image_size( 'refoulement-couverture', 1600, 900, true );
}
add_action( 'after_setup_theme', 'refoulement_setup' );

/**
 * Largeur de contenu par défaut (colonne de lecture de la charte : 680 px).
 */
function refoulement_content_width() {
	$GLOBALS['content_width'] = 680;
}
add_action( 'after_setup_theme', 'refoulement_content_width', 0 );

/**
 * Zones de widgets — une seule, en pied de page.
 */
function refoulement_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Pied de page', 'refoulement' ),
		'id'            => 'pied',
		'description'   => __( 'Bloc libre affiché dans le pied de page, sous la navigation.', 'refoulement' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget__titre">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'refoulement_widgets_init' );

/**
 * Ajoute des classes utiles sur <body>.
 *
 * @param string[] $classes Classes existantes.
 * @return string[]
 */
function refoulement_body_classes( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'surface-couverture';
	} else {
		$classes[] = 'surface-papier';
	}

	if ( ! is_active_sidebar( 'pied' ) ) {
		$classes[] = 'sans-widgets-pied';
	}

	return $classes;
}
add_filter( 'body_class', 'refoulement_body_classes' );

/**
 * Longueur des extraits : la charte affiche une phrase courte sous chaque cote.
 *
 * @return int
 */
function refoulement_excerpt_length() {
	return 26;
}
add_filter( 'excerpt_length', 'refoulement_excerpt_length' );

/**
 * Suffixe d'extrait discret (pas de « […] » par défaut).
 *
 * @return string
 */
function refoulement_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'refoulement_excerpt_more' );
