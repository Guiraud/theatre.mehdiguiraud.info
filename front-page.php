<?php
/**
 * Accueil — option 3a : couverture déchirée, carrousel vidéo, puis dossier papier tramé.
 *
 * Si une page statique est définie comme page d'accueil et qu'elle contient du
 * texte, celui-ci est affiché entre la couverture et la grille des pièces.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part( 'template-parts/couverture' );

if ( is_page() && '' !== trim( (string) get_post_field( 'post_content', get_queried_object_id() ) ) ) :
	?>
	<div class="section section--papier">
		<div class="colonne-lecture entry-content">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</div>
	<?php
endif;

get_template_part( 'template-parts/videos' );

$refoulement_pieces = refoulement_dernieres_pieces( 3 );

if ( $refoulement_pieces->have_posts() ) :
	?>
	<section class="dossier" aria-labelledby="dossier-titre">
		<div class="manchette-dossier">
			<p class="cote"><?php echo esc_html__( 'Pièces au dossier', 'refoulement' ) . ' · ' . esc_html( refoulement_mention_edition() ); ?></p>
			<h2 id="dossier-titre" class="manchette-dossier__registre">
				<a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' ) ); ?>">
					<?php esc_html_e( 'Journal de création', 'refoulement' ); ?>
				</a>
			</h2>
		</div>

		<div class="grille-pieces">
			<?php
			while ( $refoulement_pieces->have_posts() ) :
				$refoulement_pieces->the_post();
				get_template_part( 'template-parts/piece', 'carte' );
			endwhile;

			wp_reset_postdata();
			?>
		</div>
	</section>
	<?php
endif;

get_template_part( 'template-parts/lettre' );

get_footer();
