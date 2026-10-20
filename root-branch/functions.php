<?php
/**
 * Root & Branch theme bootstrap.
 *
 * Keep this file as a loader only; functionality lives in /inc.
 *
 * @package RootBranch
 */

defined( 'ABSPATH' ) || exit;

define( 'ROOT_BRANCH_VERSION', wp_get_theme()->get( 'Version' ) );

require_once get_theme_file_path( 'inc/setup.php' );
require_once get_theme_file_path( 'inc/assets.php' );
require_once get_theme_file_path( 'inc/block-styles.php' );
require_once get_theme_file_path( 'inc/patterns.php' );
require_once get_theme_file_path( 'inc/media.php' );
require_once get_theme_file_path( 'inc/query.php' );
require_once get_theme_file_path( 'inc/performance.php' );
require_once get_theme_file_path( 'inc/loader.php' );
require_once get_theme_file_path( 'inc/modal.php' );
require_once get_theme_file_path( 'inc/woocommerce.php' );
