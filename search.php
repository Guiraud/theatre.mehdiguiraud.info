<?php
/**
 * Résultats de recherche dans le dossier.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="section section--papier">
	<header class="tete-archive">
		<p class="cote cote--rouge"><?php esc_html_e( 'Recherche au dossier', 'refoulement' ); ?></p>
		<h1 class="tete-archive__titre">
			<?php
			printf(
				/* translators: %s: termes recherchés. */
				esc_html__( '« %s »', 'refoulement' ),
				esc_html( get_search_query() )
			);
			?>
		</h1>
		<div class="tete-archive__recherche"><?php get_search_form(); ?></div>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="grille-pieces grille-pieces--archive">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/piece', 'carte' );
			endwhile;
			?>
		</div>

		<?php refoulement_pagination(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/aucune', 'piece' ); ?>
	<?php endif; ?>
</div>

<?php
get_footer();
