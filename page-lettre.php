<?php
/**
 * Template Name: Recevoir les pièces
 * Template Post Type: page
 *
 * Page dédiée à l'inscription : colonne de lecture sur papier, puis le bandeau
 * d'inscription lui-même. Sert de destination au lien du pied de page, pour que
 * « S'inscrire à la newsletter » mène à une page et non à une ancre.
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
				<p class="cote cote--rouge"><?php echo esc_html( refoulement_numero_dossier() ); ?></p>
				<h1 class="tete-page__titre"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="tete-page__chapo"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</header>

			<?php if ( trim( (string) get_the_content() ) ) : ?>
				<div class="article-corps entry-content">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>
		</div>
	</article>
	<?php
endwhile;

get_template_part( 'template-parts/lettre' );

get_footer();
