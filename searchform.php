<?php
/**
 * Formulaire de recherche.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

$refoulement_id = 'recherche-' . wp_unique_id();
?>
<form class="recherche" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="ecran-lecteur" for="<?php echo esc_attr( $refoulement_id ); ?>">
		<?php esc_html_e( 'Rechercher dans le dossier', 'refoulement' ); ?>
	</label>
	<input
		id="<?php echo esc_attr( $refoulement_id ); ?>"
		class="recherche__champ"
		type="search"
		name="s"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		placeholder="<?php esc_attr_e( 'une pièce, un nom, une date', 'refoulement' ); ?>"
	>
	<button class="bouton bouton--noir recherche__bouton" type="submit"><?php esc_html_e( 'Chercher', 'refoulement' ); ?></button>
</form>
