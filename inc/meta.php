<?php
/**
 * Métadonnées du dossier : cote, numéro, durée, citation.
 *
 * La cote (« N-14 », « E-04 ») est l'élément d'identification visuelle de la
 * charte. Elle est partagée par les articles et les entretiens pour que la
 * grille d'accueil puisse les afficher indifféremment.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

/**
 * Champs enregistrés, par type de contenu.
 *
 * @return array<string, array<string, array{label:string, description:string, type:string}>>
 */
function refoulement_champs() {
	$communs = array(
		'_refoulement_numero' => array(
			'label'       => __( 'Numéro de pièce', 'refoulement' ),
			'description' => __( 'Entier. Sert à composer la cote : 14 → N-14 pour une note, E-14 pour un entretien.', 'refoulement' ),
			'type'        => 'number',
		),
		'_refoulement_cote'   => array(
			'label'       => __( 'Cote (forcée)', 'refoulement' ),
			'description' => __( 'Laisser vide pour la cote automatique. Ex. : P-02.', 'refoulement' ),
			'type'        => 'text',
		),
	);

	$entretien = array(
		'_refoulement_duree'    => array(
			'label'       => __( 'Durée', 'refoulement' ),
			'description' => __( 'Ex. : 71 min.', 'refoulement' ),
			'type'        => 'text',
		),
		'_refoulement_periode'  => array(
			'label'       => __( 'Période', 'refoulement' ),
			'description' => __( 'Ex. : Avril 2026. Affichée dans la liste des entretiens.', 'refoulement' ),
			'type'        => 'text',
		),
		'_refoulement_citation' => array(
			'label'       => __( 'Citation en exergue', 'refoulement' ),
			'description' => __( 'Une phrase, sans guillemets : ils sont ajoutés par le thème.', 'refoulement' ),
			'type'        => 'textarea',
		),
		'_refoulement_source'   => array(
			'label'       => __( 'Attribution', 'refoulement' ),
			'description' => __( 'Ex. : historienne de la psychanalyse. Jamais un nom sans accord écrit.', 'refoulement' ),
			'type'        => 'text',
		),
	);

	return array(
		'post'      => $communs,
		'entretien' => array_merge( $communs, $entretien ),
	);
}

/**
 * Enregistre les métadonnées auprès de l'API REST (éditeur de blocs).
 */
function refoulement_register_meta() {
	foreach ( refoulement_champs() as $type => $champs ) {
		foreach ( $champs as $cle => $champ ) {
			register_post_meta( $type, $cle, array(
				'type'              => 'number' === $champ['type'] ? 'integer' : 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'description'       => $champ['description'],
				'sanitize_callback' => 'number' === $champ['type'] ? 'absint' : 'sanitize_textarea_field',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			) );
		}
	}
}
add_action( 'init', 'refoulement_register_meta' );

/**
 * Ajoute la boîte « Pièce au dossier » sur les articles et les entretiens.
 */
function refoulement_add_meta_boxes() {
	foreach ( array_keys( refoulement_champs() ) as $type ) {
		add_meta_box(
			'refoulement-piece',
			__( 'Pièce au dossier', 'refoulement' ),
			'refoulement_render_meta_box',
			$type,
			'side',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'refoulement_add_meta_boxes' );

/**
 * Affiche la boîte de métadonnées.
 *
 * @param WP_Post $post Contenu en cours d'édition.
 */
function refoulement_render_meta_box( $post ) {
	$champs = refoulement_champs();

	if ( empty( $champs[ $post->post_type ] ) ) {
		return;
	}

	wp_nonce_field( 'refoulement_save_meta', 'refoulement_meta_nonce' );

	echo '<p class="description">' . esc_html__( 'Cote calculée :', 'refoulement' ) . ' <code>' . esc_html( refoulement_cote( $post ) ) . '</code></p>';

	foreach ( $champs[ $post->post_type ] as $cle => $champ ) {
		$valeur = get_post_meta( $post->ID, $cle, true );
		$id     = 'refoulement-' . sanitize_key( $cle );

		echo '<p>';
		printf( '<label for="%1$s"><strong>%2$s</strong></label><br>', esc_attr( $id ), esc_html( $champ['label'] ) );

		if ( 'textarea' === $champ['type'] ) {
			printf(
				'<textarea id="%1$s" name="%2$s" rows="3" style="width:100%%">%3$s</textarea>',
				esc_attr( $id ),
				esc_attr( $cle ),
				esc_textarea( (string) $valeur )
			);
		} else {
			printf(
				'<input id="%1$s" name="%2$s" type="%3$s" value="%4$s" style="width:100%%">',
				esc_attr( $id ),
				esc_attr( $cle ),
				esc_attr( $champ['type'] ),
				esc_attr( (string) $valeur )
			);
		}

		printf( '<span class="description">%s</span>', esc_html( $champ['description'] ) );
		echo '</p>';
	}
}

/**
 * Enregistre les métadonnées soumises.
 *
 * @param int     $post_id Identifiant du contenu.
 * @param WP_Post $post    Contenu.
 */
function refoulement_save_meta( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['refoulement_meta_nonce'] ) ) {
		return; // Sauvegarde REST : gérée par register_post_meta().
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['refoulement_meta_nonce'] ) );

	if ( ! wp_verify_nonce( $nonce, 'refoulement_save_meta' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$champs = refoulement_champs();

	if ( empty( $champs[ $post->post_type ] ) ) {
		return;
	}

	foreach ( $champs[ $post->post_type ] as $cle => $champ ) {
		if ( ! isset( $_POST[ $cle ] ) ) {
			continue;
		}

		$brut = wp_unslash( $_POST[ $cle ] );

		if ( 'number' === $champ['type'] ) {
			$valeur = '' === trim( (string) $brut ) ? '' : absint( $brut );
		} else {
			$valeur = sanitize_textarea_field( $brut );
		}

		if ( '' === $valeur ) {
			delete_post_meta( $post_id, $cle );
		} else {
			update_post_meta( $post_id, $cle, $valeur );
		}
	}
}
add_action( 'save_post', 'refoulement_save_meta', 10, 2 );
