<?php
/**
 * Types de contenu du dossier.
 *
 * Le journal de création utilise les articles natifs (cotes N-xx).
 * Les entretiens sont un type dédié (cotes E-xx) : ils ont leurs propres
 * métadonnées (durée, mois, citation) et une page d'archive distincte.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enregistre le type de contenu « entretien ».
 */
function refoulement_register_post_types() {
	$labels = array(
		'name'               => _x( 'Entretiens', 'post type general name', 'refoulement' ),
		'singular_name'      => _x( 'Entretien', 'post type singular name', 'refoulement' ),
		'menu_name'          => _x( 'Entretiens', 'admin menu', 'refoulement' ),
		'add_new'            => __( 'Ajouter', 'refoulement' ),
		'add_new_item'       => __( 'Ajouter un entretien', 'refoulement' ),
		'edit_item'          => __( 'Modifier l’entretien', 'refoulement' ),
		'new_item'           => __( 'Nouvel entretien', 'refoulement' ),
		'view_item'          => __( 'Voir l’entretien', 'refoulement' ),
		'search_items'       => __( 'Rechercher un entretien', 'refoulement' ),
		'not_found'          => __( 'Aucun entretien', 'refoulement' ),
		'not_found_in_trash' => __( 'Aucun entretien dans la corbeille', 'refoulement' ),
		'all_items'          => __( 'Tous les entretiens', 'refoulement' ),
	);

	register_post_type( 'entretien', array(
		'labels'        => $labels,
		'description'   => __( 'Conversations transcrites intégralement, versées au dossier.', 'refoulement' ),
		'public'        => true,
		'has_archive'   => true,
		'menu_icon'     => 'dashicons-microphone',
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes' ),
		'rewrite'       => array( 'slug' => 'entretiens', 'with_front' => false ),
		'show_in_rest'  => true,
		'template'      => array(
			array( 'core/paragraph', array( 'placeholder' => __( 'Chapô — une phrase sur ce que l’entretien met en jeu.', 'refoulement' ) ) ),
		),
	) );
}
add_action( 'init', 'refoulement_register_post_types' );

/**
 * Renvoie la liste des types de contenu versés au dossier (flux d'accueil).
 *
 * @return string[]
 */
function refoulement_types_du_dossier() {
	/**
	 * Filtre les types de contenu agrégés dans le flux « Pièces au dossier ».
	 *
	 * @param string[] $types Types de contenu.
	 */
	return apply_filters( 'refoulement_types_du_dossier', array( 'post', 'entretien' ) );
}

/**
 * Réécrit les colonnes de la liste d'administration des entretiens.
 *
 * @param array $colonnes Colonnes existantes.
 * @return array
 */
function refoulement_colonnes_entretien( $colonnes ) {
	$nouvelles = array();

	foreach ( $colonnes as $cle => $libelle ) {
		if ( 'title' === $cle ) {
			$nouvelles['refoulement_cote'] = __( 'Cote', 'refoulement' );
		}

		$nouvelles[ $cle ] = $libelle;

		if ( 'title' === $cle ) {
			$nouvelles['refoulement_duree'] = __( 'Durée', 'refoulement' );
		}
	}

	return $nouvelles;
}
add_filter( 'manage_entretien_posts_columns', 'refoulement_colonnes_entretien' );

/**
 * Remplit les colonnes personnalisées.
 *
 * @param string $colonne Identifiant de colonne.
 * @param int    $post_id Identifiant du contenu.
 */
function refoulement_colonne_entretien_contenu( $colonne, $post_id ) {
	if ( 'refoulement_cote' === $colonne ) {
		echo esc_html( refoulement_cote( $post_id ) );
	}

	if ( 'refoulement_duree' === $colonne ) {
		echo esc_html( (string) get_post_meta( $post_id, '_refoulement_duree', true ) );
	}
}
add_action( 'manage_entretien_posts_custom_column', 'refoulement_colonne_entretien_contenu', 10, 2 );
