<?php
/**
 * Pied de page.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

$refoulement_liens_pied = refoulement_liens_pied();
$refoulement_liens_legaux = array_intersect_key( $refoulement_liens_pied, array_flip( array( 'mentions', 'confidentialite' ) ) );
?>
	</main><!-- .site__contenu -->

	<footer class="pied">
		<div class="pied__interieur">
			<div class="pied__colonne pied__colonne--identite">
				<p class="pied__manchette"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
				<?php if ( get_bloginfo( 'description' ) ) : ?>
					<p class="pied__accroche"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
				<?php endif; ?>
				<p class="pied__cote"><?php echo esc_html( refoulement_numero_dossier() . ' · ' . refoulement_mention_edition() ); ?></p>
			</div>

			<div class="pied__colonne pied__colonne--informations">
				<p class="pied__titre"><?php esc_html_e( 'Informations', 'refoulement' ); ?></p>

				<?php if ( $refoulement_liens_legaux ) : ?>
					<nav aria-label="<?php esc_attr_e( 'Informations légales', 'refoulement' ); ?>">
						<ul class="menu-pied menu-pied--legal">
							<?php foreach ( $refoulement_liens_legaux as $refoulement_cle => $refoulement_lien ) : ?>
								<li class="menu-pied__element menu-pied__element--<?php echo esc_attr( $refoulement_cle ); ?>">
									<a href="<?php echo esc_url( $refoulement_lien['url'] ); ?>"><?php echo esc_html( $refoulement_lien['label'] ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endif; ?>

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

			<div class="pied__colonne pied__colonne--lettre">
				<p class="pied__titre"><?php esc_html_e( 'Recevoir les pièces', 'refoulement' ); ?></p>
				<p class="pied__accroche"><?php esc_html_e( 'Suivre les nouvelles pièces versées au dossier.', 'refoulement' ); ?></p>
				<a class="pied__appel" href="<?php echo esc_url( $refoulement_liens_pied['newsletter']['url'] ); ?>"><?php esc_html_e( 'S’inscrire à la newsletter', 'refoulement' ); ?></a>
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
