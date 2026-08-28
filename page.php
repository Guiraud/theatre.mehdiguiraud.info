<?php
/**
 * Page — colonne de lecture sur papier.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'page-simple' ); ?>>
		<div class="section section--papier">
			<header class="tete-page colonne-lecture">
				<p class="cote cote--rouge">
					<?php
					$refoulement_parent = wp_get_post_parent_id( get_the_ID() );
					echo esc_html( $refoulement_parent ? get_the_title( $refoulement_parent ) : refoulement_numero_dossier() );
					?>
				</p>
				<h1 class="tete-page__titre"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="tete-page__chapo"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="page-simple__image colonne-lecture"><?php the_post_thumbnail( 'refoulement-piece' ); ?></figure>
			<?php endif; ?>

			<div class="article-corps entry-content">
				<?php
				the_content();

				wp_link_pages( array(
					'before' => '<nav class="pages-liees cote">' . esc_html__( 'Suite :', 'refoulement' ) . ' ',
					'after'  => '</nav>',
				) );
				?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
