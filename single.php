<?php
/**
 * Article du journal — option 2d : en-tête noir, lecture sur papier, notes en marge.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'piece-longue' ); ?>>
		<header class="entete-piece">
			<div class="entete-piece__interieur">
				<p class="cote cote--nuit"><?php echo esc_html( refoulement_surtitre() ); ?></p>
				<h1 class="entete-piece__titre"><?php the_title(); ?></h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="entete-piece__chapo"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="piece-longue__couverture">
				<?php the_post_thumbnail( 'refoulement-couverture' ); ?>
				<?php if ( wp_get_attachment_caption( get_post_thumbnail_id() ) ) : ?>
					<figcaption class="cote"><?php echo esc_html( (string) wp_get_attachment_caption( get_post_thumbnail_id() ) ); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>

		<div class="section section--papier">
			<div class="article-corps entry-content">
				<?php
				the_content();

				wp_link_pages( array(
					'before' => '<nav class="pages-liees cote">' . esc_html__( 'Suite de la pièce :', 'refoulement' ) . ' ',
					'after'  => '</nav>',
				) );
				?>
			</div>

			<?php
			$refoulement_categories = get_the_category_list( ' · ' );
			$refoulement_etiquettes = get_the_tag_list( '', ' · ' );

			if ( $refoulement_categories || $refoulement_etiquettes ) :
				?>
				<footer class="piece-longue__pied colonne-lecture">
					<?php
					if ( $refoulement_categories ) {
						printf( '<p class="cote">%s %s</p>', esc_html__( 'Versé sous :', 'refoulement' ), wp_kses_post( $refoulement_categories ) );
					}

					if ( $refoulement_etiquettes ) {
						printf( '<p class="cote">%s %s</p>', esc_html__( 'Mots-clés :', 'refoulement' ), wp_kses_post( $refoulement_etiquettes ) );
					}
					?>
				</footer>
			<?php endif; ?>
		</div>

		<nav class="navigation-pieces" aria-label="<?php esc_attr_e( 'Pièces voisines', 'refoulement' ); ?>">
			<?php
			previous_post_link( '<span class="navigation-pieces__lien navigation-pieces__lien--precedent">%link</span>', '<span class="cote">' . esc_html__( 'Pièce précédente', 'refoulement' ) . '</span>%title' );
			next_post_link( '<span class="navigation-pieces__lien navigation-pieces__lien--suivant">%link</span>', '<span class="cote">' . esc_html__( 'Pièce suivante', 'refoulement' ) . '</span>%title' );
			?>
		</nav>

		<?php
		if ( comments_open() || get_comments_number() ) {
			echo '<div class="section section--papier"><div class="colonne-lecture">';
			comments_template();
			echo '</div></div>';
		}
		?>
	</article>
	<?php
endwhile;

get_template_part( 'template-parts/lettre' );

get_footer();
