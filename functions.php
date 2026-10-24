<?php
/**
 * Le Refoulement de Freud — amorçage du thème.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

define( 'REFOULEMENT_VERSION', '1.1.5' );
define( 'REFOULEMENT_DIR', get_template_directory() );
define( 'REFOULEMENT_URI', get_template_directory_uri() );

require_once REFOULEMENT_DIR . '/inc/setup.php';
require_once REFOULEMENT_DIR . '/inc/enqueue.php';
require_once REFOULEMENT_DIR . '/inc/post-types.php';
require_once REFOULEMENT_DIR . '/inc/meta.php';
require_once REFOULEMENT_DIR . '/inc/template-tags.php';
require_once REFOULEMENT_DIR . '/inc/queries.php';
require_once REFOULEMENT_DIR . '/inc/customizer.php';
require_once REFOULEMENT_DIR . '/inc/footer.php';
require_once REFOULEMENT_DIR . '/inc/newsletter.php';
require_once REFOULEMENT_DIR . '/inc/patterns.php';
require_once REFOULEMENT_DIR . '/inc/seo.php';
