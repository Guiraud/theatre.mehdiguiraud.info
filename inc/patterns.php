<?php
/**
 * Compositions de blocs reprenant les motifs de la charte.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enregistre la catégorie et les compositions.
 */
function refoulement_register_patterns() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	register_block_pattern_category( 'refoulement', array(
		'label' => __( 'Le Refoulement de Freud', 'refoulement' ),
	) );

	// Tour de parole d'un entretien (locuteur en marge, texte en colonne).
	register_block_pattern( 'refoulement/tour-de-parole', array(
		'title'       => __( 'Entretien — tour de parole', 'refoulement' ),
		'description' => __( 'Locuteur en marge, réponse en colonne de lecture. À répéter pour chaque échange.', 'refoulement' ),
		'categories'  => array( 'refoulement' ),
		'keywords'    => array( 'entretien', 'dialogue', 'transcription' ),
		'content'     => '<!-- wp:group {"className":"tour-de-parole","layout":{"type":"default"}} -->
<div class="wp-block-group tour-de-parole"><!-- wp:paragraph {"className":"tour-de-parole__locuteur"} -->
<p class="tour-de-parole__locuteur">MG</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"tour-de-parole__texte","layout":{"type":"default"}} -->
<div class="wp-block-group tour-de-parole__texte"><!-- wp:paragraph -->
<p>' . esc_html__( 'Question posée.', 'refoulement' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"tour-de-parole tour-de-parole--temoin","layout":{"type":"default"}} -->
<div class="wp-block-group tour-de-parole tour-de-parole--temoin"><!-- wp:paragraph {"className":"tour-de-parole__locuteur"} -->
<p class="tour-de-parole__locuteur">' . esc_html__( 'Témoin', 'refoulement' ) . '</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"tour-de-parole__texte","layout":{"type":"default"}} -->
<div class="wp-block-group tour-de-parole__texte"><!-- wp:paragraph -->
<p>' . esc_html__( 'Réponse transcrite intégralement.', 'refoulement' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->',
	) );

	// Citation versée au dossier : filet rouge à gauche, attribution en cote.
	register_block_pattern( 'refoulement/citation-versee', array(
		'title'       => __( 'Citation versée au dossier', 'refoulement' ),
		'description' => __( 'Citation en Bodoni italique, filet rouge, attribution en capitales.', 'refoulement' ),
		'categories'  => array( 'refoulement' ),
		'content'     => '<!-- wp:quote {"className":"citation-versee"} -->
<blockquote class="wp-block-quote citation-versee"><!-- wp:paragraph -->
<p>' . esc_html__( 'Il n’y a pas eu de démenti. Il y a eu un déplacement de la charge de la preuve.', 'refoulement' ) . '</p>
<!-- /wp:paragraph --><cite>' . esc_html__( 'Entretien 01 — historienne de la psychanalyse', 'refoulement' ) . '</cite></blockquote>
<!-- /wp:quote -->',
	) );

	// Note d'apparat critique, destinée à la marge des articles.
	register_block_pattern( 'refoulement/note-marge', array(
		'title'       => __( 'Note en marge', 'refoulement' ),
		'description' => __( 'Note d’apparat critique. Placée dans le contenu, elle se replie sous le texte sur petit écran.', 'refoulement' ),
		'categories'  => array( 'refoulement' ),
		'content'     => '<!-- wp:group {"className":"note-marge","layout":{"type":"default"}} -->
<div class="wp-block-group note-marge"><!-- wp:heading {"level":4,"className":"note-marge__titre"} -->
<h4 class="wp-block-heading note-marge__titre">' . esc_html__( 'Note 1', 'refoulement' ) . '</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"note-marge__texte"} -->
<p class="note-marge__texte">' . esc_html__( 'Lettre 139 de la correspondance Freud–Fliess, éd. Masson.', 'refoulement' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->',
	) );

	// Repères chronologiques de la page « Le projet ».
	register_block_pattern( 'refoulement/calendrier', array(
		'title'       => __( 'Calendrier du projet', 'refoulement' ),
		'description' => __( 'Trois repères datés, filets pleine largeur.', 'refoulement' ),
		'categories'  => array( 'refoulement' ),
		'content'     => '<!-- wp:group {"className":"calendrier","layout":{"type":"default"}} -->
<div class="wp-block-group calendrier"><!-- wp:group {"className":"calendrier__ligne","layout":{"type":"default"}} -->
<div class="wp-block-group calendrier__ligne"><!-- wp:paragraph {"className":"calendrier__periode"} -->
<p class="calendrier__periode">2025—2026</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"calendrier__fait"} -->
<p class="calendrier__fait">' . esc_html__( 'Enquête et entretiens', 'refoulement' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"calendrier__ligne","layout":{"type":"default"}} -->
<div class="wp-block-group calendrier__ligne"><!-- wp:paragraph {"className":"calendrier__periode"} -->
<p class="calendrier__periode">2026</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"calendrier__fait"} -->
<p class="calendrier__fait">' . esc_html__( 'Écriture, lectures publiques', 'refoulement' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"calendrier__ligne","layout":{"type":"default"}} -->
<div class="wp-block-group calendrier__ligne"><!-- wp:paragraph {"className":"calendrier__periode"} -->
<p class="calendrier__periode">2027</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"calendrier__fait"} -->
<p class="calendrier__fait">' . esc_html__( 'Création', 'refoulement' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->',
	) );

	// Fac-similé : trame de papier, légèrement pivoté.
	register_block_pattern( 'refoulement/fac-simile', array(
		'title'       => __( 'Fac-similé', 'refoulement' ),
		'description' => __( 'Document versé au dossier, monté comme un tirage épinglé.', 'refoulement' ),
		'categories'  => array( 'refoulement' ),
		'content'     => '<!-- wp:image {"className":"fac-simile"} -->
<figure class="wp-block-image fac-simile"><img alt="' . esc_attr__( 'Document versé au dossier', 'refoulement' ) . '"/><figcaption class="wp-element-caption">' . esc_html__( 'P-02 · fac-similé annoté', 'refoulement' ) . '</figcaption></figure>
<!-- /wp:image -->',
	) );
}
add_action( 'init', 'refoulement_register_patterns' );

/**
 * Retire les compositions distantes de WordPress.org : la charte est fermée.
 */
add_filter( 'should_load_remote_block_patterns', '__return_false' );
