<?php
/**
 * Template Name: À propos — portrait
 * Template Post Type: page
 *
 * Portrait en cadrage large à gauche, texte et contacts à droite (option 1h,
 * carte de droite), redécliné dans la palette v2.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'portrait' ); ?>>
		<div class="section section--papier">
			<div class="portrait__grille">
				<div class="portrait__image">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'refoulement-piece' ); ?>
						<?php if ( wp_get_attachment_caption( get_post_thumbnail_id() ) ) : ?>
							<p class="cote"><?php echo esc_html( (string) wp_get_attachment_caption( get_post_thumbnail_id() ) ); ?></p>
						<?php endif; ?>
					<?php else : ?>
						<p class="portrait__reserve cote"><?php esc_html_e( 'Portrait — cadrage large, duotone noir / rouge dossier', 'refoulement' ); ?></p>
					<?php endif; ?>
				</div>

				<div class="portrait__texte">
					<p class="cote cote--rouge"><?php esc_html_e( 'À propos', 'refoulement' ); ?></p>
					<h1 class="portrait__titre"><?php the_title(); ?></h1>

					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				</div>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
