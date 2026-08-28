<?php
/**
 * Bandeau de navigation — barre noire, manchette à gauche, menu en Oswald.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;
?>
<header class="bandeau">
	<div class="bandeau__interieur">
		<?php refoulement_manchette(); ?>

		<button
			class="bandeau__bascule"
			type="button"
			aria-expanded="false"
			aria-controls="menu-principal"
			data-bascule-menu
		>
			<span class="bandeau__bascule-barres" aria-hidden="true"></span>
			<span class="ecran-lecteur"><?php esc_html_e( 'Ouvrir la navigation', 'refoulement' ); ?></span>
		</button>

		<nav id="menu-principal" class="bandeau__nav" aria-label="<?php esc_attr_e( 'Navigation principale', 'refoulement' ); ?>">
			<?php
			if ( has_nav_menu( 'principal' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'principal',
					'container'      => false,
					'menu_class'     => 'menu-principal',
					'depth'          => 1,
					'fallback_cb'    => false,
				) );
			} else {
				echo '<ul class="menu-principal">';

				$pages = wp_list_pages( array(
					'title_li' => '',
					'depth'    => 1,
					'echo'     => false,
					'number'   => 4,
				) );

				echo wp_kses_post( (string) $pages );

				printf(
					'<li><a href="%s">%s</a></li>',
					esc_url( get_post_type_archive_link( 'entretien' ) ? get_post_type_archive_link( 'entretien' ) : home_url( '/' ) ),
					esc_html__( 'Entretiens', 'refoulement' )
				);

				echo '</ul>';
			}
			?>
		</nav>
	</div>
</header>
