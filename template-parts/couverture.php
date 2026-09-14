<?php
/**
 * Couverture d'accueil — option 3a de la charte.
 *
 * Déchirure rouge à gauche (clip-path), papier tramé à droite. Sur petit écran,
 * la déchirure passe en bandeau haut et la déchirure devient horizontale.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

$refoulement_titre_recto = (string) refoulement_reglage( 'refoulement_titre_recto' );
$refoulement_mots        = preg_split( '/\s+/', trim( $refoulement_titre_recto ), 2 );

/*
 * Les deux boutons de la planche 3a sont toujours affichés : sans adresse
 * renseignée, le premier ouvre le journal et le second la page « Le projet »
 * si elle existe, à défaut l'accueil.
 */
$refoulement_journal  = get_permalink( (int) get_option( 'page_for_posts' ) );
$refoulement_projet   = get_page_by_path( 'le-projet' );
$refoulement_bouton_1 = (string) refoulement_reglage( 'refoulement_bouton_1_url' );
$refoulement_bouton_2 = (string) refoulement_reglage( 'refoulement_bouton_2_url' );

if ( '' === $refoulement_bouton_1 ) {
	$refoulement_bouton_1 = $refoulement_journal ? $refoulement_journal : home_url( '/' );
}

if ( '' === $refoulement_bouton_2 ) {
	$refoulement_bouton_2 = $refoulement_projet ? get_permalink( $refoulement_projet ) : home_url( '/' );
}

$refoulement_compteurs   = refoulement_compteurs();
?>
<section class="couverture">
	<div class="couverture__dechirure" aria-hidden="true"></div>

	<div class="couverture__recto">
		<p class="cote cote--nuit">
			<?php echo esc_html( refoulement_numero_dossier() . ' · ' . (string) refoulement_reglage( 'refoulement_etat_dossier' ) ); ?>
		</p>

		<?php
		/*
		 * Le titre est coupé en deux par la déchirure : la première partie est
		 * imprimée sur le rouge, la seconde sur le papier. Le <h1> porte le
		 * titre entier pour les lecteurs d'écran ; la partie visible à droite
		 * est répétée dans la colonne papier et masquée à l'accessibilité.
		 */
		?>
		<h1 class="couverture__titre">
			<span class="couverture__titre-recto">
				<?php
				foreach ( (array) $refoulement_mots as $refoulement_index => $refoulement_mot ) {
					echo esc_html( $refoulement_mot );

					if ( 0 === $refoulement_index && count( $refoulement_mots ) > 1 ) {
						echo '<br>';
					}
				}
				?>
			</span>
			<span class="ecran-lecteur"> <?php echo esc_html( (string) refoulement_reglage( 'refoulement_titre_verso' ) ); ?></span>
		</h1>

		<?php get_template_part( 'template-parts/ecouter', null, array( 'variante' => 'couverture' ) ); ?>

		<?php if ( $refoulement_compteurs ) : ?>
			<dl class="compteurs">
				<?php foreach ( $refoulement_compteurs as $refoulement_compteur ) : ?>
					<div class="compteurs__item">
						<dt class="compteurs__valeur"><?php echo esc_html( $refoulement_compteur['valeur'] ); ?></dt>
						<dd class="compteurs__libelle"><?php echo esc_html( $refoulement_compteur['libelle'] ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>
	</div>

	<div class="couverture__verso">
		<p class="couverture__titre-verso" aria-hidden="true"><?php echo esc_html( (string) refoulement_reglage( 'refoulement_titre_verso' ) ); ?></p>

		<p class="couverture__chapo"><?php echo esc_html( (string) refoulement_reglage( 'refoulement_chapo' ) ); ?></p>

		<div class="couverture__boutons">
			<?php if ( refoulement_reglage( 'refoulement_bouton_1_texte' ) ) : ?>
				<a class="bouton bouton--noir" href="<?php echo esc_url( $refoulement_bouton_1 ); ?>">
					<?php echo esc_html( (string) refoulement_reglage( 'refoulement_bouton_1_texte' ) ); ?>
				</a>
			<?php endif; ?>

			<?php if ( refoulement_reglage( 'refoulement_bouton_2_texte' ) ) : ?>
				<a class="bouton bouton--contour" href="<?php echo esc_url( $refoulement_bouton_2 ); ?>">
					<?php echo esc_html( (string) refoulement_reglage( 'refoulement_bouton_2_texte' ) ); ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="decoupes">
			<?php
			foreach ( array( 'refoulement_piece_1', 'refoulement_piece_2' ) as $refoulement_rang => $refoulement_champ ) {
				$refoulement_id = absint( refoulement_reglage( $refoulement_champ ) );

				printf( '<div class="decoupes__piece decoupes__piece--%d">', absint( $refoulement_rang ) + 1 );

				if ( $refoulement_id ) {
					echo wp_get_attachment_image( $refoulement_id, 'refoulement-piece', false, array( 'class' => 'decoupes__image', 'loading' => 'eager' ) );
				}

				echo '</div>';
			}
			?>

			<?php if ( refoulement_reglage( 'refoulement_legende_pieces' ) ) : ?>
				<p class="decoupes__legende"><?php echo esc_html( (string) refoulement_reglage( 'refoulement_legende_pieces' ) ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
