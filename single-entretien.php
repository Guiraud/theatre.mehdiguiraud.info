<?php
/**
 * Page d'entretien — structure de l'option 1g, redéclinée dans la palette v2.
 *
 * Le dialogue est composé dans l'éditeur avec la composition
 * « Entretien — tour de parole » (locuteur en marge, réponse en colonne).
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'entretien' ); ?>>
		<header class="entete-piece">
			<div class="entete-piece__interieur">
				<p class="cote cote--nuit">
					<?php
					printf(
						/* translators: 1: cote, 2: ligne « période · durée · transcription ». */
						esc_html__( 'Entretien %1$s · %2$s', 'refoulement' ),
						esc_html( refoulement_cote() ),
						esc_html( refoulement_ligne_entretien() )
					);
					?>
				</p>

				<h1 class="entete-piece__titre"><?php the_title(); ?></h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="entete-piece__chapo"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
		</header>

		<div class="section section--papier">
			<?php
			$refoulement_citation = refoulement_citation();
			$refoulement_source   = (string) get_post_meta( get_the_ID(), '_refoulement_source', true );

			if ( '' !== $refoulement_citation ) :
				?>
				<blockquote class="citation-versee citation-versee--exergue">
					<p><?php echo wp_kses_post( $refoulement_citation ); ?></p>
					<?php if ( '' !== $refoulement_source ) : ?>
						<cite><?php echo esc_html( $refoulement_source ); ?></cite>
					<?php endif; ?>
				</blockquote>
			<?php endif; ?>

			<div class="entretien__transcription entry-content">
				<?php the_content(); ?>
			</div>
		</div>

		<nav class="navigation-pieces" aria-label="<?php esc_attr_e( 'Entretiens voisins', 'refoulement' ); ?>">
			<?php
			previous_post_link( '<span class="navigation-pieces__lien navigation-pieces__lien--precedent">%link</span>', '<span class="cote">' . esc_html__( 'Entretien précédent', 'refoulement' ) . '</span>%title' );
			next_post_link( '<span class="navigation-pieces__lien navigation-pieces__lien--suivant">%link</span>', '<span class="cote">' . esc_html__( 'Entretien suivant', 'refoulement' ) . '</span>%title' );
			?>
		</nav>
	</article>
	<?php
endwhile;

get_footer();
