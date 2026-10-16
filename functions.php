<?php
/**
 * Jawad block theme bootstrap.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

define( 'JD_VERSION', '1.5.0' );
define( 'JD_DIR', get_template_directory() );
define( 'JD_URI', get_template_directory_uri() );

require_once JD_DIR . '/inc/helpers.php';
require_once JD_DIR . '/inc/options.php';
require_once JD_DIR . '/inc/setup.php';
require_once JD_DIR . '/inc/performance.php';
require_once JD_DIR . '/inc/post-types.php';
require_once JD_DIR . '/inc/meta-boxes.php';
require_once JD_DIR . '/inc/blocks.php';
require_once JD_DIR . '/inc/forms.php';
require_once JD_DIR . '/inc/seo.php';
require_once JD_DIR . '/inc/starter-content.php';
require_once JD_DIR . '/inc/migration.php';
