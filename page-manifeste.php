<?php
/**
 * Template Name: Le projet — manifeste
 * Template Post Type: page
 *
 * En-tête noir puis colonne de lecture (option 1h, carte de gauche), redécliné
 * dans la palette v2. La chronologie s'insère avec la composition
 * « Calendrier du projet ».
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'manifeste' ); ?>>
		<header class="entete-piece">
			<div class="entete-piece__interieur">
				<p class="cote cote--nuit"><?php echo esc_html( refoulement_numero_dossier() ); ?></p>
				<h1 class="entete-piece__titre"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="entete-piece__chapo"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
		</header>

		<div class="section section--papier">
			<div class="article-corps entry-content">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_template_part( 'template-parts/lettre' );

get_footer();
