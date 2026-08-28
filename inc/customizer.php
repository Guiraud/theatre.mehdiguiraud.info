<?php
/**
 * Outil de personnalisation : couverture d'accueil et bandeau d'inscription.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

/**
 * Accepte uniquement les URL publiques des formulaires intégrés Brevo.
 *
 * @param string $url URL proposée.
 * @return string URL nettoyée, ou chaîne vide si elle n'est pas autorisée.
 */
function refoulement_sanitize_brevo_embed_url( $url ) {
	$url = esc_url_raw( (string) $url, array( 'https' ) );

	if ( '' === $url ) {
		return '';
	}

	$host = (string) wp_parse_url( $url, PHP_URL_HOST );
	$path = (string) wp_parse_url( $url, PHP_URL_PATH );

	if ( ! preg_match( '/(?:^|\.)sibforms\.com$/i', $host ) ) {
		return '';
	}

	if ( ! str_starts_with( $path, '/v2/serve/' ) ) {
		return '';
	}

	return $url;
}

/**
 * Accepte uniquement un identifiant de formulaire du plugin Brevo.
 *
 * `absint()` ne convient pas : il transformerait « -3 » en 3.
 *
 * @param mixed $valeur Identifiant proposé.
 * @return int Identifiant positif, ou 0 si aucun formulaire n'est désigné.
 */
function refoulement_sanitize_brevo_form_id( $valeur ) {
	$id = filter_var( $valeur, FILTER_VALIDATE_INT );

	return ( false !== $id && $id > 0 ) ? $id : 0;
}

/**
 * Réglages du thème et valeurs par défaut.
 *
 * @return array<string, array{defaut:mixed, label:string, type:string, section:string, description?:string}>
 */
function refoulement_reglages() {
	return array(
		// Couverture.
		'refoulement_numero_dossier'   => array(
			'defaut'  => __( 'Dossier n° 1897', 'refoulement' ),
			'label'   => __( 'Numéro de dossier', 'refoulement' ),
			'type'    => 'text',
			'section' => 'refoulement_couverture',
		),
		'refoulement_etat_dossier'     => array(
			'defaut'  => __( 'enquête en cours', 'refoulement' ),
			'label'   => __( 'État du dossier', 'refoulement' ),
			'type'    => 'text',
			'section' => 'refoulement_couverture',
		),
		'refoulement_titre_recto'      => array(
			'defaut'      => __( 'Le Refoulement', 'refoulement' ),
			'label'       => __( 'Titre — partie sur la déchirure rouge', 'refoulement' ),
			'type'        => 'text',
			'section'     => 'refoulement_couverture',
			'description' => __( 'Un retour à la ligne est inséré automatiquement après le premier mot.', 'refoulement' ),
		),
		'refoulement_titre_verso'      => array(
			'defaut'  => __( 'de Freud', 'refoulement' ),
			'label'   => __( 'Titre — partie sur le papier', 'refoulement' ),
			'type'    => 'text',
			'section' => 'refoulement_couverture',
		),
		'refoulement_chapo'            => array(
			'defaut'  => __( 'Enquête dramaturgique sur la théorie de la séduction, abandonnée en 1897, et sur ce que les institutions ont cessé d’entendre : la parole des enfants, celle des mères.', 'refoulement' ),
			'label'   => __( 'Chapô', 'refoulement' ),
			'type'    => 'textarea',
			'section' => 'refoulement_couverture',
		),
		'refoulement_bouton_1_texte'   => array(
			'defaut'  => __( 'Ouvrir le dossier', 'refoulement' ),
			'label'   => __( 'Bouton principal — libellé', 'refoulement' ),
			'type'    => 'text',
			'section' => 'refoulement_couverture',
		),
		'refoulement_bouton_1_url'     => array(
			'defaut'  => '',
			'label'   => __( 'Bouton principal — adresse', 'refoulement' ),
			'type'    => 'url',
			'section' => 'refoulement_couverture',
		),
		'refoulement_bouton_2_texte'   => array(
			'defaut'  => __( 'Le projet', 'refoulement' ),
			'label'   => __( 'Bouton secondaire — libellé', 'refoulement' ),
			'type'    => 'text',
			'section' => 'refoulement_couverture',
		),
		'refoulement_bouton_2_url'     => array(
			'defaut'  => '',
			'label'   => __( 'Bouton secondaire — adresse', 'refoulement' ),
			'type'    => 'url',
			'section' => 'refoulement_couverture',
		),
		'refoulement_compteurs'        => array(
			'defaut'      => "14|notes\n07|entretiens\n2027|création",
			'label'       => __( 'Compteurs', 'refoulement' ),
			'type'        => 'textarea',
			'section'     => 'refoulement_couverture',
			'description' => __( 'Une ligne par compteur, au format « valeur|libellé ». Trois lignes au maximum.', 'refoulement' ),
		),
		'refoulement_legende_pieces'   => array(
			'defaut'  => __( 'P-01 Vienne 1896 · P-02 lettre 139', 'refoulement' ),
			'label'   => __( 'Légende des découpes', 'refoulement' ),
			'type'    => 'text',
			'section' => 'refoulement_couverture',
		),
		'refoulement_piece_1'          => array(
			'defaut'      => 0,
			'label'       => __( 'Découpe 1 (image)', 'refoulement' ),
			'type'        => 'image',
			'section'     => 'refoulement_couverture',
			'description' => __( 'Silhouettes, objets, documents. Jamais de visage de victime ni de scène reconstituée.', 'refoulement' ),
		),
		'refoulement_piece_2'          => array(
			'defaut'  => 0,
			'label'   => __( 'Découpe 2 (image)', 'refoulement' ),
			'type'    => 'image',
			'section' => 'refoulement_couverture',
		),

		// Bandeau d'inscription.
		'refoulement_lettre_titre'     => array(
			'defaut'  => __( 'Recevoir les pièces', 'refoulement' ),
			'label'   => __( 'Titre du bandeau', 'refoulement' ),
			'type'    => 'text',
			'section' => 'refoulement_lettre',
		),
		'refoulement_lettre_brevo_form_id' => array(
			'defaut'      => 0,
			'label'       => __( 'Identifiant du formulaire du plugin Brevo', 'refoulement' ),
			'type'        => 'brevo_form_id',
			'section'     => 'refoulement_lettre',
			'description' => __( 'Numéro affiché dans Brevo → Formulaires. Rend le formulaire dans la page, stylé par le thème. Nécessite une clé API renseignée dans le plugin ; sinon le thème retombe sur l’iframe ci-dessous.', 'refoulement' ),
		),
		'refoulement_lettre_brevo_url' => array(
			'defaut'      => '',
			'label'       => __( 'Adresse du formulaire intégré Brevo', 'refoulement' ),
			'type'        => 'brevo_url',
			'section'     => 'refoulement_lettre',
			'description' => __( 'Adresse HTTPS trouvée dans le code iframe fourni par Brevo (`src`). Elle doit appartenir à sibforms.com.', 'refoulement' ),
		),
		'refoulement_lettre_action'    => array(
			'defaut'      => '',
			'label'       => __( 'Adresse de traitement du formulaire', 'refoulement' ),
			'type'        => 'url',
			'section'     => 'refoulement_lettre',
			'description' => __( 'URL fournie par votre service d’envoi. Laissée vide, le bandeau propose un lien de contact par courriel.', 'refoulement' ),
		),
		'refoulement_lettre_champ'     => array(
			'defaut'      => 'email',
			'label'       => __( 'Nom du champ adresse', 'refoulement' ),
			'type'        => 'text',
			'section'     => 'refoulement_lettre',
			'description' => __( 'Attribut « name » attendu par le service (souvent email, EMAIL ou fields[email]).', 'refoulement' ),
		),
		'refoulement_lettre_bouton'    => array(
			'defaut'  => __( 'S’inscrire', 'refoulement' ),
			'label'   => __( 'Libellé du bouton', 'refoulement' ),
			'type'    => 'text',
			'section' => 'refoulement_lettre',
		),
		'refoulement_lettre_contact'   => array(
			'defaut'  => '',
			'label'   => __( 'Adresse de contact (repli)', 'refoulement' ),
			'type'    => 'email',
			'section' => 'refoulement_lettre',
		),
	);
}

/**
 * Retourne la valeur d'un réglage.
 *
 * @param string $cle Identifiant du réglage.
 * @return mixed
 */
function refoulement_reglage( $cle ) {
	$reglages = refoulement_reglages();
	$defaut   = isset( $reglages[ $cle ] ) ? $reglages[ $cle ]['defaut'] : '';

	return get_theme_mod( $cle, $defaut );
}

/**
 * Déclare les sections et contrôles.
 *
 * @param WP_Customize_Manager $wp_customize Gestionnaire.
 */
function refoulement_customize_register( $wp_customize ) {
	$wp_customize->add_panel( 'refoulement', array(
		'title'       => __( 'Le Refoulement de Freud', 'refoulement' ),
		'description' => __( 'Couverture d’accueil et bandeau d’inscription.', 'refoulement' ),
		'priority'    => 20,
	) );

	$wp_customize->add_section( 'refoulement_couverture', array(
		'title' => __( 'Couverture d’accueil', 'refoulement' ),
		'panel' => 'refoulement',
	) );

	$wp_customize->add_section( 'refoulement_lettre', array(
		'title' => __( 'Recevoir les pièces', 'refoulement' ),
		'panel' => 'refoulement',
	) );

	foreach ( refoulement_reglages() as $cle => $reglage ) {
		$sanitize = 'sanitize_text_field';

		switch ( $reglage['type'] ) {
			case 'textarea':
				$sanitize = 'sanitize_textarea_field';
				break;
			case 'url':
				$sanitize = 'esc_url_raw';
				break;
			case 'brevo_url':
				$sanitize = 'refoulement_sanitize_brevo_embed_url';
				break;
			case 'brevo_form_id':
				$sanitize = 'refoulement_sanitize_brevo_form_id';
				break;
			case 'email':
				$sanitize = 'sanitize_email';
				break;
			case 'image':
				$sanitize = 'absint';
				break;
		}

		$wp_customize->add_setting( $cle, array(
			'default'           => $reglage['defaut'],
			'sanitize_callback' => $sanitize,
			'transport'         => 'refresh',
		) );

		if ( 'image' === $reglage['type'] ) {
			$wp_customize->add_control(
				new WP_Customize_Media_Control( $wp_customize, $cle, array(
					'label'       => $reglage['label'],
					'description' => isset( $reglage['description'] ) ? $reglage['description'] : '',
					'section'     => $reglage['section'],
					'mime_type'   => 'image',
				) )
			);
			continue;
		}

		$wp_customize->add_control( $cle, array(
			'label'       => $reglage['label'],
			'description' => isset( $reglage['description'] ) ? $reglage['description'] : '',
			'section'     => $reglage['section'],
			'type'        => match ( $reglage['type'] ) {
				'textarea'          => 'textarea',
				'url', 'brevo_url'  => 'url',
				'brevo_form_id'     => 'number',
				default             => $reglage['type'],
			},
		) );
	}
}
add_action( 'customize_register', 'refoulement_customize_register' );

/**
 * Découpe le réglage « compteurs » en paires valeur / libellé.
 *
 * @return array<int, array{valeur:string, libelle:string}>
 */
function refoulement_compteurs() {
	$lignes    = preg_split( '/\R/', (string) refoulement_reglage( 'refoulement_compteurs' ) );
	$compteurs = array();

	foreach ( (array) $lignes as $ligne ) {
		$ligne = trim( $ligne );

		if ( '' === $ligne ) {
			continue;
		}

		$parties = array_map( 'trim', explode( '|', $ligne, 2 ) );

		$compteurs[] = array(
			'valeur'  => $parties[0],
			'libelle' => isset( $parties[1] ) ? $parties[1] : '',
		);
	}

	return array_slice( $compteurs, 0, 3 );
}
