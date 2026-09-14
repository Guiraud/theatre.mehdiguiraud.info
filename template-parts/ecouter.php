<?php
/**
 * Bandeau « Écouter le podcast » — liens multi-plateformes.
 *
 * Les adresses se règlent dans Apparence → Personnaliser → Écouter le podcast.
 * Sans aucune URL, le bandeau reste silencieux.
 *
 * Args :
 *   variante = '' | 'couverture' — compact sous le titre d'accueil.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

$refoulement_liens = refoulement_liens_podcast();

if ( array() === $refoulement_liens ) {
	return;
}

$refoulement_variante = ( isset( $args['variante'] ) && 'couverture' === $args['variante'] ) ? 'couverture' : '';

if ( 'couverture' === $refoulement_variante ) :
	?>
	<nav class="ecouter ecouter--couverture" aria-label="<?php esc_attr_e( 'Écouter le podcast', 'refoulement' ); ?>">
		<ul class="ecouter__liens">
			<?php foreach ( $refoulement_liens as $refoulement_plateforme ) : ?>
				<li>
					<a
						class="ecouter__badge <?php echo esc_attr( $refoulement_plateforme['classe'] ); ?>"
						href="<?php echo esc_url( $refoulement_plateforme['url'] ); ?>"
						target="_blank"
						rel="noopener noreferrer"
					>
						<svg class="ecouter__icone" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><?php echo $refoulement_plateforme['icone']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- chemins SVG statiques du thème. ?></svg>
						<span><?php echo esc_html( $refoulement_plateforme['label'] ); ?></span>
						<span class="ecran-lecteur"><?php esc_html_e( '(nouvelle fenêtre)', 'refoulement' ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
	return;
endif;

$refoulement_titre = (string) refoulement_reglage( 'refoulement_ecouter_titre' );
$refoulement_chapo = (string) refoulement_reglage( 'refoulement_ecouter_chapo' );

if ( '' === $refoulement_titre ) {
	$refoulement_titre = __( 'Écouter « Le refoulement de Freud »', 'refoulement' );
}
?>
<aside class="ecouter" aria-labelledby="ecouter-titre">
	<div class="ecouter__interieur">
		<p class="cote cote--nuit"><?php esc_html_e( 'Podcast', 'refoulement' ); ?></p>

		<h2 id="ecouter-titre" class="ecouter__titre"><?php echo esc_html( $refoulement_titre ); ?></h2>

		<?php if ( '' !== $refoulement_chapo ) : ?>
			<p class="ecouter__chapo"><?php echo esc_html( $refoulement_chapo ); ?></p>
		<?php endif; ?>

		<ul class="ecouter__liens">
			<?php foreach ( $refoulement_liens as $refoulement_plateforme ) : ?>
				<li>
					<a
						class="ecouter__badge <?php echo esc_attr( $refoulement_plateforme['classe'] ); ?>"
						href="<?php echo esc_url( $refoulement_plateforme['url'] ); ?>"
						target="_blank"
						rel="noopener noreferrer"
					>
						<svg class="ecouter__icone" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><?php echo $refoulement_plateforme['icone']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- chemins SVG statiques du thème. ?></svg>
						<span><?php echo esc_html( $refoulement_plateforme['label'] ); ?></span>
						<span class="ecran-lecteur"><?php esc_html_e( '(nouvelle fenêtre)', 'refoulement' ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</aside>
