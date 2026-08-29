<?php
/**
 * Liens du pied de page.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

/**
 * Identifiants des pages légales publiées.
 *
 * Sert au pied de page et permet à la navigation du haut de les écarter :
 * ces pages appartiennent au pied, pas au fil éditorial.
 *
 * @return array<int, int> Identifiants, mentions légales d'abord.
 */
function refoulement_pages_legales() {
	$pages = array();

	$mentions = absint( refoulement_reglage( 'refoulement_mentions_legales_page_id' ) );

	if ( $mentions && 'publish' === get_post_status( $mentions ) ) {
		$pages[] = $mentions;
	}

	$confidentialite = absint( get_option( 'wp_page_for_privacy_policy' ) );

	if ( $confidentialite && 'publish' === get_post_status( $confidentialite ) ) {
		$pages[] = $confidentialite;
	}

	return $pages;
}

/**
 * Adresse de la page d'inscription à la newsletter.
 *
 * Deux destinations, par ordre de préférence : une page du site dédiée à la
 * lettre, puis la page hébergée par Brevo. Le formulaire n'étant plus ajouté au
 * bas des autres gabarits, aucune ancre ne sert de repli : sans destination
 * réelle, le pied n'affiche pas d'appel plutôt qu'un lien mort.
 *
 * @param int $page_lettre Identifiant d'une page dédiée déjà vérifiée, ou 0.
 * @return string Adresse absolue, ou chaîne vide si aucune destination.
 */
function refoulement_adresse_lettre( $page_lettre = 0 ) {
	if ( $page_lettre ) {
		$adresse = get_permalink( $page_lettre );

		if ( is_string( $adresse ) && '' !== $adresse ) {
			return $adresse;
		}
	}

	return refoulement_sanitize_brevo_embed_url( (string) refoulement_reglage( 'refoulement_lettre_brevo_url' ) );
}

/**
 * Résout les liens légaux et l'ancre de la newsletter.
 *
 * Seules les pages réellement publiées produisent un lien : une page en
 * brouillon, supprimée ou non désignée est omise plutôt que rendue morte.
 *
 * @return array<string, array{label:string, url:string}> Liens dans l'ordre d'affichage.
 */
function refoulement_liens_pied() {
	$liens = array();

	$page_mentions = absint( refoulement_reglage( 'refoulement_mentions_legales_page_id' ) );

	if ( $page_mentions && 'publish' === get_post_status( $page_mentions ) ) {
		$adresse_mentions = get_permalink( $page_mentions );

		if ( is_string( $adresse_mentions ) && '' !== $adresse_mentions ) {
			$liens['mentions'] = array(
				'label' => __( 'Mentions légales', 'refoulement' ),
				'url'   => $adresse_mentions,
			);
		}
	}

	$confidentialite = (string) get_privacy_policy_url();

	if ( '' !== $confidentialite ) {
		$liens['confidentialite'] = array(
			'label' => __( 'Confidentialité', 'refoulement' ),
			'url'   => $confidentialite,
		);
	}

	$page_lettre = absint( refoulement_reglage( 'refoulement_lettre_page_id' ) );
	$page_lettre = ( $page_lettre && 'publish' === get_post_status( $page_lettre ) ) ? $page_lettre : 0;

	$adresse_lettre = refoulement_adresse_lettre( $page_lettre );

	// Sur la page dédiée elle-même, l'appel n'aurait nulle part où mener.
	if ( '' !== $adresse_lettre && ( ! $page_lettre || ! is_page( $page_lettre ) ) ) {
		$liens['newsletter'] = array(
			'label' => __( 'Recevoir les pièces', 'refoulement' ),
			'url'   => $adresse_lettre,
		);
	}

	return $liens;
}
