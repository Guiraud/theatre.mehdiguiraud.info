<?php
/**
 * Carrousel vidéo de l'accueil — épisodes et shorts YouTube.
 *
 * La liste se règle dans Apparence → Personnaliser → Carrousel vidéo.
 * Sans JavaScript, chaque vignette mène à la vidéo sur YouTube ; avec, elle
 * charge le lecteur youtube-nocookie sur place, au clic seulement.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

$refoulement_videos = refoulement_videos();

if ( array() === $refoulement_videos ) {
	return;
}

$refoulement_titre = (string) refoulement_reglage( 'refoulement_videos_titre' );

if ( '' === $refoulement_titre ) {
	$refoulement_titre = __( 'Les entretiens en images', 'refoulement' );
}
?>
<section class="videos" aria-labelledby="videos-titre" data-carrousel>
	<div class="videos__entete">
		<div>
			<p class="cote cote--nuit"><?php esc_html_e( 'Pièces filmées', 'refoulement' ); ?></p>
			<h2 id="videos-titre" class="videos__titre"><?php echo esc_html( $refoulement_titre ); ?></h2>
		</div>

		<div class="videos__commandes" data-carrousel-commandes hidden>
			<button type="button" class="videos__fleche" data-carrousel-precedent aria-controls="videos-piste">
				<span aria-hidden="true">←</span>
				<span class="ecran-lecteur"><?php esc_html_e( 'Vidéos précédentes', 'refoulement' ); ?></span>
			</button>
			<button type="button" class="videos__fleche" data-carrousel-suivant aria-controls="videos-piste">
				<span aria-hidden="true">→</span>
				<span class="ecran-lecteur"><?php esc_html_e( 'Vidéos suivantes', 'refoulement' ); ?></span>
			</button>
		</div>
	</div>

	<ul id="videos-piste" class="videos__piste" data-carrousel-piste tabindex="0" aria-label="<?php esc_attr_e( 'Vidéos, défilement horizontal', 'refoulement' ); ?>">
		<?php foreach ( $refoulement_videos as $refoulement_video ) : ?>
			<li class="videos__carte videos__carte--<?php echo esc_attr( $refoulement_video['format'] ); ?>">
				<div class="videos__cadre">
					<a
						class="videos__lanceur"
						href="<?php echo esc_url( $refoulement_video['url'] ); ?>"
						target="_blank"
						rel="noopener noreferrer"
						data-video-id="<?php echo esc_attr( $refoulement_video['id'] ); ?>"
						data-video-titre="<?php echo esc_attr( $refoulement_video['titre'] ); ?>"
					>
						<img
							class="videos__vignette"
							src="<?php echo esc_url( 'https://i.ytimg.com/vi/' . $refoulement_video['id'] . '/hqdefault.jpg' ); ?>"
							alt=""
							width="480"
							height="360"
							loading="lazy"
							decoding="async"
						>
						<span class="videos__lecture" aria-hidden="true"></span>
						<span class="ecran-lecteur">
							<?php
							/* translators: %s : titre de la vidéo. */
							printf( esc_html__( 'Lire « %s »', 'refoulement' ), esc_html( $refoulement_video['titre'] ) );
							?>
						</span>
					</a>
				</div>
				<p class="videos__legende" aria-hidden="true"><?php echo esc_html( $refoulement_video['titre'] ); ?></p>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
