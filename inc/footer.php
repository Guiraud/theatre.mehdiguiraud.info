<?php
/**
 * Liens du pied de page.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

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
