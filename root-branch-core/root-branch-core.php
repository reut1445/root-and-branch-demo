<?php
/**
 * Plugin Name:       Root & Branch Core
 * Description:       Site functionality for Root & Branch that must survive a theme change: the sign-up form block (with an admin list of sign-ups and CSV export) and external links for media posts.
 * Version:           1.0.0
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Author:            Reut Kishon
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       root-branch-core
 *
 * @package RootBranchCore
 */

defined( 'ABSPATH' ) || exit;

define( 'ROOT_BRANCH_CORE_VERSION', '1.0.0' );
define( 'ROOT_BRANCH_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'ROOT_BRANCH_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once ROOT_BRANCH_CORE_DIR . 'includes/signups.php';
require_once ROOT_BRANCH_CORE_DIR . 'includes/external-links.php';

add_action(
	'init',
	static function () {
		register_block_type( ROOT_BRANCH_CORE_DIR . 'blocks/signup-form' );
	}
);
