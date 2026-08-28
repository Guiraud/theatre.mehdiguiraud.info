<?php
/**
 * Pièce absente du dossier (404).
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="section section--papier">
	<div class="colonne-lecture aucune-piece">
		<p class="cote cote--rouge"><?php esc_html_e( 'Pièce manquante · 404', 'refoulement' ); ?></p>
		<h1><?php esc_html_e( 'Cette pièce n’est pas au dossier.', 'refoulement' ); ?></h1>
		<p><?php esc_html_e( 'L’adresse demandée ne correspond à rien de versé ici. Le journal et les entretiens restent accessibles depuis la navigation.', 'refoulement' ); ?></p>
		<?php get_search_form(); ?>
	</div>
</div>

<?php
get_footer();
