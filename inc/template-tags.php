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
 * Liens d'écoute podcast (plateformes renseignées uniquement).
 *
 * @return array<string, array{url:string, label:string, classe:string, icone:string}>
 */
function refoulement_liens_podcast() {
	$plateformes = array(
		'spotify' => array(
			'url'    => (string) refoulement_reglage( 'refoulement_ecouter_spotify' ),
			'label'  => __( 'Spotify', 'refoulement' ),
			'classe' => 'ecouter__badge--spotify',
			'icone'  => '<path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.485 17.306c-.218.358-.684.47-1.042.252-2.855-1.745-6.449-2.14-10.682-1.173-.41.093-.815-.164-.908-.573-.094-.41.164-.816.573-.909 4.636-1.06 8.608-.611 11.807 1.348.358.218.47.684.252 1.056zm1.464-3.26c-.274.446-.86.588-1.306.314-3.268-2.008-8.25-2.59-12.115-1.416-.499.152-1.03-.135-1.182-.634-.152-.499.135-1.03.634-1.182 4.412-1.339 9.898-.693 13.655 1.612.446.274.588.86.314 1.306zm.126-3.41c-3.918-2.327-10.377-2.541-14.123-1.403-.6.182-1.238-.163-1.42-.763-.182-.6.163-1.238.763-1.42 4.304-1.307 11.436-1.054 15.952 1.627.54.32.716 1.02.396 1.56-.32.54-1.02.716-1.56.399z"/>',
		),
		'apple'   => array(
			'url'    => (string) refoulement_reglage( 'refoulement_ecouter_apple' ),
			'label'  => __( 'Apple Podcasts', 'refoulement' ),
			'classe' => 'ecouter__badge--apple',
			'icone'  => '<path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 4.92c.62-.75 1.04-1.8.93-2.85-.9.04-1.99.6-2.63 1.35-.57.66-1.07 1.73-.93 2.76 1.01.08 2.02-.51 2.63-1.26z"/>',
		),
		'youtube' => array(
			'url'    => (string) refoulement_reglage( 'refoulement_ecouter_youtube' ),
			'label'  => __( 'YouTube', 'refoulement' ),
			'classe' => 'ecouter__badge--youtube',
			'icone'  => '<path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>',
		),
		'deezer'  => array(
			'url'    => (string) refoulement_reglage( 'refoulement_ecouter_deezer' ),
			'label'  => __( 'Deezer', 'refoulement' ),
			'classe' => 'ecouter__badge--deezer',
			'icone'  => '<path d="M6.06 17.06H0V24h6.06v-6.94zm8.97-8.53H8.97V24h6.06V8.53zm8.97-8.53h-6.06V24H24V0z"/>',
		),
		'rss'     => array(
			'url'    => (string) refoulement_reglage( 'refoulement_ecouter_rss' ),
			'label'  => __( 'Flux RSS', 'refoulement' ),
			'classe' => 'ecouter__badge--rss',
			'icone'  => '<path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19 7.38 20 6.18 20C5 20 4 19 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27V4.44m0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93V10.1z"/>',
		),
	);

	// Repli : si le Customizer a enregistré une chaîne vide, le défaut WP disparaît.
	$replis = array(
		'spotify' => 'https://open.spotify.com/show/7sKjKaoeWA5cSevsv90Moy',
		'apple'   => 'https://podcasts.apple.com/fr/podcast/le-refoulement-de-freud/id6811654704',
		'youtube' => 'https://www.youtube.com/playlist?list=PLU9lfNmykraI',
		'deezer'  => 'https://www.deezer.com/show/1003573932',
		'rss'     => 'https://www.vodio.fr/rssmedias-2729.xml',
	);

	foreach ( $replis as $cle => $url ) {
		if ( '' === $plateformes[ $cle ]['url'] ) {
			$plateformes[ $cle ]['url'] = $url;
		}
	}

	$liens = array();

	foreach ( $plateformes as $cle => $plateforme ) {
		$url = esc_url( $plateforme['url'] );

		if ( '' === $url ) {
			continue;
		}

		$plateforme['url'] = $url;
		$liens[ $cle ]     = $plateforme;
	}

	return $liens;
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
