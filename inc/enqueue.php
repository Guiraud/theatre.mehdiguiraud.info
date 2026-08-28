<?php
/**
 * Feuilles de style, polices auto-hébergées et scripts.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

/**
 * Retourne le numéro de version d'un fichier du thème (mtime en dev, version sinon).
 *
 * @param string $rel Chemin relatif au thème.
 * @return string
 */
function refoulement_asset_version( $rel ) {
	$path = REFOULEMENT_DIR . '/' . ltrim( $rel, '/' );

	if ( file_exists( $path ) ) {
		return (string) filemtime( $path );
	}

	return REFOULEMENT_VERSION;
}

/**
 * Met en file les styles et scripts du site public.
 */
function refoulement_enqueue_assets() {
	wp_enqueue_style(
		'refoulement-fonts',
		REFOULEMENT_URI . '/assets/css/fonts.css',
		array(),
		refoulement_asset_version( 'assets/css/fonts.css' )
	);

	wp_enqueue_style(
		'refoulement',
		REFOULEMENT_URI . '/assets/css/theme.css',
		array( 'refoulement-fonts' ),
		refoulement_asset_version( 'assets/css/theme.css' )
	);

	// L'en-tête du thème (style.css) ne porte que les métadonnées ; on le déclare
	// tout de même pour les extensions qui s'y accrochent.
	wp_register_style( 'refoulement-style', get_stylesheet_uri(), array( 'refoulement' ), REFOULEMENT_VERSION );

	wp_enqueue_script(
		'refoulement',
		REFOULEMENT_URI . '/assets/js/theme.js',
		array(),
		refoulement_asset_version( 'assets/js/theme.js' ),
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'refoulement_enqueue_assets' );

/**
 * Précharge les fontes réellement utilisées au-dessus de la ligne de flottaison :
 * le titre Bodoni 900, l'étiquette Oswald 500 et le corps Atkinson 400.
 */
function refoulement_preload_fonts() {
	$fichiers = array(
		'bodoni-moda-900-normal-latin.woff2',
		'oswald-500-normal-latin.woff2',
		'atkinson-400-normal-latin.woff2',
	);

	foreach ( $fichiers as $fichier ) {
		if ( ! file_exists( REFOULEMENT_DIR . '/assets/fonts/' . $fichier ) ) {
			continue;
		}

		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( REFOULEMENT_URI . '/assets/fonts/' . $fichier )
		);
	}
}
add_action( 'wp_head', 'refoulement_preload_fonts', 1 );

/**
 * Styles de l'éditeur : les polices auto-hébergées ne sont pas résolues par
 * add_editor_style() dans l'éditeur de site, on les ajoute explicitement.
 */
function refoulement_enqueue_editor_assets() {
	wp_enqueue_style(
		'refoulement-fonts-editeur',
		REFOULEMENT_URI . '/assets/css/fonts.css',
		array(),
		refoulement_asset_version( 'assets/css/fonts.css' )
	);
}
add_action( 'enqueue_block_editor_assets', 'refoulement_enqueue_editor_assets' );
