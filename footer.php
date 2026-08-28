<?php
/**
 * Pied de page.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;
?>
	</main><!-- .site__contenu -->

	<footer class="pied">
		<div class="pied__interieur">
			<div class="pied__colonne">
				<p class="pied__manchette"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
				<?php if ( get_bloginfo( 'description' ) ) : ?>
					<p class="pied__accroche"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
				<?php endif; ?>
				<p class="pied__cote"><?php echo esc_html( refoulement_numero_dossier() . ' · ' . refoulement_mention_edition() ); ?></p>
			</div>

			<div class="pied__colonne">
				<?php
				if ( has_nav_menu( 'pied' ) ) {
					wp_nav_menu( array(
						'theme_location' => 'pied',
						'container'      => false,
						'menu_class'     => 'menu-pied',
						'depth'          => 1,
						'fallback_cb'    => false,
					) );
				}

				if ( is_active_sidebar( 'pied' ) ) {
					dynamic_sidebar( 'pied' );
				}
				?>
			</div>
		</div>

		<p class="pied__mention">
			<?php
			printf(
				/* translators: %s: année en cours. */
				esc_html__( '© %s — tous droits réservés.', 'refoulement' ),
				esc_html( wp_date( 'Y' ) )
			);
			?>
		</p>
	</footer>
</div><!-- .site -->

<?php wp_footer(); ?>
</body>
</html>
