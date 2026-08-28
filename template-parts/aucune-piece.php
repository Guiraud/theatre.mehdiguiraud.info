<?php
/**
 * Message affiché lorsqu'aucune pièce ne correspond.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="colonne-lecture aucune-piece">
	<p class="cote cote--rouge"><?php esc_html_e( 'Dossier vide', 'refoulement' ); ?></p>

	<?php if ( is_search() ) : ?>
		<h2><?php esc_html_e( 'Aucune pièce ne porte cette mention.', 'refoulement' ); ?></h2>
		<p><?php esc_html_e( 'Essayez une autre formulation, ou parcourez le journal.', 'refoulement' ); ?></p>
		<?php get_search_form(); ?>
	<?php else : ?>
		<h2><?php esc_html_e( 'Rien n’a encore été versé ici.', 'refoulement' ); ?></h2>
		<p><?php esc_html_e( 'Les notes de travail et les entretiens paraissent au fil de l’enquête.', 'refoulement' ); ?></p>
	<?php endif; ?>
</div>
