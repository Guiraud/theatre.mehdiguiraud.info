<?php
/**
 * Liste des entretiens — structure de l'option 1f, redéclinée dans la palette v2.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

get_header();

$refoulement_objet = get_post_type_object( 'entretien' );
?>

<div class="section section--papier">
	<header class="tete-archive">
		<p class="cote cote--rouge"><?php echo esc_html( refoulement_numero_dossier() ); ?></p>
		<h1 class="tete-archive__titre"><?php post_type_archive_title(); ?></h1>
		<?php if ( $refoulement_objet && $refoulement_objet->description ) : ?>
			<p class="tete-archive__chapo"><?php echo esc_html( $refoulement_objet->description ); ?></p>
		<?php endif; ?>
	</header>

	<?php if ( have_posts() ) : ?>
		<ol class="liste-entretiens">
			<?php
			while ( have_posts() ) :
				the_post();
				$refoulement_citation = refoulement_citation();
				?>
				<li class="entretien-ligne">
					<span class="entretien-ligne__cote"><?php echo esc_html( refoulement_cote() ); ?></span>

					<div class="entretien-ligne__corps">
						<h2 class="entretien-ligne__titre">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>

						<?php if ( '' !== $refoulement_citation ) : ?>
							<p class="entretien-ligne__citation"><?php echo wp_kses_post( $refoulement_citation ); ?></p>
						<?php endif; ?>

						<p class="cote"><?php echo esc_html( refoulement_ligne_entretien() ); ?></p>
					</div>
				</li>
			<?php endwhile; ?>
		</ol>

		<?php refoulement_pagination(); ?>

	<?php else : ?>
		<?php get_template_part( 'template-parts/aucune', 'piece' ); ?>
	<?php endif; ?>
</div>

<?php
get_template_part( 'template-parts/lettre' );

get_footer();
