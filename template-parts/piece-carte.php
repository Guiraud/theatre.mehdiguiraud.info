<?php
/**
 * Une pièce du dossier dans la grille d'accueil : cote rouge, titre Bodoni, phrase.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;
?>
<article <?php post_class( 'piece' ); ?>>
	<p class="cote cote--rouge"><?php echo esc_html( refoulement_ligne_cote() ); ?></p>

	<h3 class="piece__titre">
		<a class="piece__lien" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h3>

	<?php if ( has_excerpt() || get_the_content() ) : ?>
		<p class="piece__extrait"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
	<?php endif; ?>
</article>
