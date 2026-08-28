<?php
/**
 * Requêtes du dossier.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

/**
 * Retourne les dernières pièces versées, articles et entretiens confondus.
 *
 * @param int   $nombre  Nombre de pièces.
 * @param int[] $exclure Identifiants à exclure.
 * @return WP_Query
 */
function refoulement_dernieres_pieces( $nombre = 3, $exclure = array() ) {
	return new WP_Query( array(
		'post_type'           => refoulement_types_du_dossier(),
		'post_status'         => 'publish',
		'posts_per_page'      => (int) $nombre,
		'post__not_in'        => array_map( 'absint', (array) $exclure ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	) );
}

/**
 * Inclut les entretiens dans les résultats de recherche.
 *
 * @param WP_Query $query Requête principale.
 */
function refoulement_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_search() ) {
		$query->set( 'post_type', array_merge( refoulement_types_du_dossier(), array( 'page' ) ) );
	}

	if ( $query->is_post_type_archive( 'entretien' ) ) {
		$query->set( 'posts_per_page', 20 );
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
}
add_action( 'pre_get_posts', 'refoulement_pre_get_posts' );
