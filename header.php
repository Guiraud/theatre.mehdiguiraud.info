<?php
/**
 * En-tête du document.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="lien-evitement" href="#contenu"><?php esc_html_e( 'Aller au contenu', 'refoulement' ); ?></a>

<div class="site">
	<?php get_template_part( 'template-parts/bandeau', 'nav' ); ?>
	<main id="contenu" class="site__contenu">
