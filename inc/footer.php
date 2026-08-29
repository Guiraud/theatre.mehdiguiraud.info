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

	$liens['newsletter'] = array(
		'label' => __( 'Recevoir les pièces', 'refoulement' ),
		'url'   => home_url( '/#recevoir-les-pieces' ),
	);

	return $liens;
}
