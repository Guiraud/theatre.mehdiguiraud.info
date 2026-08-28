<?php
/**
 * Fonctions d'affichage réutilisées par les gabarits.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

/**
 * Préfixe de cote par type de contenu (N pour note, E pour entretien).
 *
 * @param string $type Type de contenu.
 * @return string
 */
function refoulement_prefixe_cote( $type ) {
	$prefixes = array(
		'post'      => 'N',
		'entretien' => 'E',
		'page'      => 'D',
		'attachment' => 'P',
	);

	/**
	 * Filtre les préfixes de cote.
	 *
	 * @param array $prefixes Correspondance type de contenu → préfixe.
	 */
	$prefixes = apply_filters( 'refoulement_prefixes_cote', $prefixes );

	return isset( $prefixes[ $type ] ) ? $prefixes[ $type ] : 'D';
}

/**
 * Retourne la cote d'archive d'un contenu (« N-14 », « E-04 »).
 *
 * Ordre de résolution : cote forcée, puis numéro de pièce, puis identifiant.
 *
 * @param int|WP_Post|null $post Contenu.
 * @return string
 */
function refoulement_cote( $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return '';
	}

	$forcee = (string) get_post_meta( $post->ID, '_refoulement_cote', true );

	if ( '' !== trim( $forcee ) ) {
		return $forcee;
	}

	$numero = get_post_meta( $post->ID, '_refoulement_numero', true );

	if ( '' === $numero || null === $numero ) {
		$numero = $post->ID;
	}

	return sprintf(
		'%s-%02d',
		refoulement_prefixe_cote( $post->post_type ),
		(int) $numero
	);
}

/**
 * Date courte au format de la charte : 14.05.26.
 *
 * @param int|WP_Post|null $post Contenu.
 * @return string
 */
function refoulement_date_courte( $post = null ) {
	return (string) get_the_date( 'd.m.y', $post );
}

/**
 * Ligne de cote « N-14 · 14.05.26 ».
 *
 * @param int|WP_Post|null $post Contenu.
 * @return string
 */
function refoulement_ligne_cote( $post = null ) {
	return refoulement_cote( $post ) . ' · ' . refoulement_date_courte( $post );
}

/**
 * Nom lisible du type de contenu, pour les surtitres.
 *
 * @param int|WP_Post|null $post Contenu.
 * @return string
 */
function refoulement_registre( $post = null ) {
	$post = get_post( $post );

	if ( $post && 'entretien' === $post->post_type ) {
		return __( 'entretien', 'refoulement' );
	}

	return __( 'journal de création', 'refoulement' );
}

/**
 * Surtitre d'une page singulière : « Pièce N-14 · journal de création · 14 mai 2026 ».
 *
 * @param int|WP_Post|null $post Contenu.
 * @return string
 */
function refoulement_surtitre( $post = null ) {
	return sprintf(
		/* translators: 1: cote, 2: registre, 3: date longue. */
		__( 'Pièce %1$s · %2$s · %3$s', 'refoulement' ),
		refoulement_cote( $post ),
		refoulement_registre( $post ),
		get_the_date( '', $post )
	);
}

/**
 * Affiche la mention d'édition du dossier (« édition du 14 mai 2026 »).
 *
 * @return string
 */
function refoulement_mention_edition() {
	$derniers = get_posts( array(
		'post_type'        => refoulement_types_du_dossier(),
		'posts_per_page'   => 1,
		'post_status'      => 'publish',
		'suppress_filters' => false,
		'fields'           => 'ids',
	) );

	$date = $derniers ? get_the_date( '', $derniers[0] ) : current_time( get_option( 'date_format' ) );

	return sprintf(
		/* translators: %s: date de la dernière pièce publiée. */
		__( 'édition du %s', 'refoulement' ),
		$date
	);
}

/**
 * Numéro de dossier affiché dans la titraille (réglable dans l'outil de personnalisation).
 *
 * @return string
 */
function refoulement_numero_dossier() {
	return (string) get_theme_mod( 'refoulement_numero_dossier', __( 'Dossier n° 1897', 'refoulement' ) );
}

/**
 * Citation en exergue d'un entretien, avec ses guillemets français.
 *
 * @param int|WP_Post|null $post Contenu.
 * @return string
 */
function refoulement_citation( $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return '';
	}

	$citation = trim( (string) get_post_meta( $post->ID, '_refoulement_citation', true ) );

	if ( '' === $citation ) {
		return '';
	}

	return '«&nbsp;' . $citation . '&nbsp;»';
}

/**
 * Ligne de description d'un entretien : « Avril 2026 · 71 min · transcription intégrale ».
 *
 * @param int|WP_Post|null $post Contenu.
 * @return string
 */
function refoulement_ligne_entretien( $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return '';
	}

	$morceaux = array_filter( array(
		(string) get_post_meta( $post->ID, '_refoulement_periode', true ),
		(string) get_post_meta( $post->ID, '_refoulement_duree', true ),
		__( 'transcription intégrale', 'refoulement' ),
	) );

	return implode( ' · ', $morceaux );
}

/**
 * Affiche le titre du site en registre « manchette » (logo si défini).
 */
function refoulement_manchette() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}

	// Sur l'accueil, le <h1> appartient à la couverture : la manchette reste un <p>.
	$balise = ( is_home() && ! is_front_page() ) ? 'h1' : 'p';

	printf(
		'<%1$s class="manchette"><a class="manchette__lien" href="%2$s" rel="home">%3$s</a></%1$s>',
		esc_attr( $balise ),
		esc_url( home_url( '/' ) ),
		esc_html( get_bloginfo( 'name' ) )
	);
}

/**
 * Pagination sobre : « ← Pièces précédentes / Pièces suivantes → ».
 */
function refoulement_pagination() {
	the_posts_pagination( array(
		'mid_size'           => 1,
		'prev_text'          => __( 'Pièces précédentes', 'refoulement' ),
		'next_text'          => __( 'Pièces suivantes', 'refoulement' ),
		'screen_reader_text' => __( 'Navigation dans le dossier', 'refoulement' ),
		'class'              => 'pagination-dossier',
	) );
}
