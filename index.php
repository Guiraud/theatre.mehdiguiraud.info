<?php
/**
 * Gabarit générique — journal de création et archives.
 *
 * Reprend la manchette et la grille « Pièces au dossier » de l'accueil, en
 * pleine page sur fond papier.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="section section--papier">
	<header class="tete-archive">
		<?php if ( is_home() && ! is_front_page() ) : ?>
			<p class="cote cote--rouge"><?php echo esc_html( refoulement_numero_dossier() ); ?></p>
			<h1 class="tete-archive__titre">
				<?php
				$refoulement_page_articles = (int) get_option( 'page_for_posts' );
				echo esc_html( $refoulement_page_articles ? get_the_title( $refoulement_page_articles ) : __( 'Journal de création', 'refoulement' ) );
				?>
			</h1>
			<?php
			if ( $refoulement_page_articles && get_post( $refoulement_page_articles )->post_excerpt ) {
				printf( '<p class="tete-archive__chapo">%s</p>', esc_html( get_post( $refoulement_page_articles )->post_excerpt ) );
			}
			?>
		<?php elseif ( is_archive() ) : ?>
			<p class="cote cote--rouge"><?php echo esc_html( refoulement_numero_dossier() ); ?></p>
			<h1 class="tete-archive__titre"><?php the_archive_title(); ?></h1>
			<?php the_archive_description( '<div class="tete-archive__chapo">', '</div>' ); ?>
		<?php else : ?>
			<h1 class="tete-archive__titre"><?php esc_html_e( 'Pièces au dossier', 'refoulement' ); ?></h1>
		<?php endif; ?>
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
