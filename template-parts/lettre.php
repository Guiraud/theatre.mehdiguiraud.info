<?php
/**
 * Bandeau « Recevoir les pièces ».
 *
 * Le thème ne traite aucune inscription : il fournit la maquette et délègue.
 * Cinq cas, dans l'ordre :
 *   1. le filtre `refoulement_lettre_formulaire` retourne un formulaire (extension) ;
 *   2. le plugin Brevo rend un formulaire natif via son shortcode ;
 *   3. une adresse de formulaire intégré Brevo est renseignée (iframe) ;
 *   4. une adresse de traitement générique est renseignée ;
 *   5. repli : lien de contact par courriel.
 *
 * Le shortcode `sibwp_form` n'est déclaré que si une clé API est enregistrée dans
 * le plugin, et rend une chaîne vide si l'identifiant est inconnu. Les deux cas
 * retombent sur l'iframe.
 *
 * L'iframe Brevo porte `no-lazyload` : Smush déplace sinon `src` vers `data-src`
 * et laisse un SVG transparent que son script ne restaure pas.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

$refoulement_titre = (string) refoulement_reglage( 'refoulement_lettre_titre' );

/**
 * Filtre le formulaire d'inscription du bandeau.
 *
 * Retourner du HTML depuis une extension court-circuite le formulaire du thème.
 *
 * @param string $formulaire HTML du formulaire (vide par défaut).
 */
$refoulement_formulaire = apply_filters( 'refoulement_lettre_formulaire', '' );

$refoulement_brevo   = refoulement_sanitize_brevo_embed_url( (string) refoulement_reglage( 'refoulement_lettre_brevo_url' ) );

$refoulement_form_id = refoulement_sanitize_brevo_form_id( refoulement_reglage( 'refoulement_lettre_brevo_form_id' ) );
$refoulement_natif   = '';

if ( 0 !== $refoulement_form_id && shortcode_exists( 'sibwp_form' ) ) {
	$refoulement_natif = refoulement_accessibiliser_formulaire_brevo( trim( do_shortcode( sprintf( '[sibwp_form id=%d]', $refoulement_form_id ) ) ) );
}

$refoulement_action  = (string) refoulement_reglage( 'refoulement_lettre_action' );
$refoulement_contact = (string) refoulement_reglage( 'refoulement_lettre_contact' );

if ( '' === $refoulement_formulaire && '' === $refoulement_natif && '' === $refoulement_brevo && '' === $refoulement_action && '' === $refoulement_contact ) {
	return;
}
?>
<aside class="lettre" aria-labelledby="lettre-titre">
	<?php
	$refoulement_iframe_seule = '' === $refoulement_formulaire && '' === $refoulement_natif && '' !== $refoulement_brevo;
	$refoulement_natif_seul   = '' === $refoulement_formulaire && '' !== $refoulement_natif;

	$refoulement_variante = '';

	if ( $refoulement_iframe_seule ) {
		$refoulement_variante = ' lettre__interieur--brevo';
	} elseif ( $refoulement_natif_seul ) {
		$refoulement_variante = ' lettre__interieur--natif';
	}
	?>
	<div class="lettre__interieur<?php echo esc_attr( $refoulement_variante ); ?>">
		<h2 id="lettre-titre" class="lettre__titre<?php echo $refoulement_iframe_seule ? ' ecran-lecteur' : ''; ?>"><?php echo esc_html( $refoulement_titre ); ?></h2>

		<?php if ( '' !== $refoulement_formulaire ) : ?>
			<div class="lettre__formulaire"><?php echo wp_kses_post( $refoulement_formulaire ); ?></div>

		<?php elseif ( '' !== $refoulement_natif ) : ?>
			<div class="lettre__formulaire lettre__formulaire--brevo"><?php echo $refoulement_natif; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- balisage du plugin Brevo, construit à partir d'un identifiant entier validé. ?></div>

		<?php elseif ( '' !== $refoulement_brevo ) : ?>
			<iframe
				class="lettre__iframe no-lazyload"
				title="<?php echo esc_attr( sprintf( __( 'Formulaire d’inscription à la newsletter %s', 'refoulement' ), $refoulement_titre ) ); ?>"
				src="<?php echo esc_url( $refoulement_brevo ); ?>"
				width="540"
				height="700"
				loading="lazy"
				referrerpolicy="strict-origin-when-cross-origin"
				scrolling="auto"
			></iframe>

		<?php elseif ( '' !== $refoulement_action ) : ?>
			<form class="lettre__formulaire" action="<?php echo esc_url( $refoulement_action ); ?>" method="post" target="_blank">
				<label class="ecran-lecteur" for="lettre-adresse"><?php esc_html_e( 'Votre adresse électronique', 'refoulement' ); ?></label>
				<input
					id="lettre-adresse"
					class="lettre__champ"
					type="email"
					name="<?php echo esc_attr( (string) refoulement_reglage( 'refoulement_lettre_champ' ) ); ?>"
					placeholder="<?php esc_attr_e( 'votre@adresse.fr', 'refoulement' ); ?>"
					autocomplete="email"
					required
				>
				<button class="bouton bouton--rouge lettre__bouton" type="submit">
					<?php echo esc_html( (string) refoulement_reglage( 'refoulement_lettre_bouton' ) ); ?>
				</button>
			</form>

		<?php else : ?>
			<p class="lettre__repli">
				<a class="bouton bouton--rouge" href="<?php echo esc_url( 'mailto:' . $refoulement_contact . '?subject=' . rawurlencode( __( 'Recevoir les pièces', 'refoulement' ) ) ); ?>">
					<?php esc_html_e( 'Écrire pour recevoir les pièces', 'refoulement' ); ?>
				</a>
			</p>
		<?php endif; ?>
	</div>
</aside>
