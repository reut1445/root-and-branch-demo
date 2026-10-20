<?php
/**
 * Plugin Name:       Root & Branch Starter Content
 * Description:       One-time import of the Root & Branch pages, media-coverage posts, videos, events and photographs. Run it from Tools → Root & Branch content (or `wp root-branch import`), then deactivate and delete this plugin.
 * Version:           1.0.0
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Author:            Reut Kishon
 * License:           GPL-2.0-or-later
 * Text Domain:       root-branch-starter
 *
 * @package RootBranchStarter
 */

defined( 'ABSPATH' ) || exit;

define( 'ROOT_BRANCH_STARTER_DIR', plugin_dir_path( __FILE__ ) );

require_once ROOT_BRANCH_STARTER_DIR . 'includes/class-importer.php';

add_action(
	'admin_menu',
	static function () {
		add_management_page(
			__( 'Root & Branch content', 'root-branch-starter' ),
			__( 'Root & Branch content', 'root-branch-starter' ),
			'manage_options',
			'root-branch-starter',
			'root_branch_starter_screen'
		);
	}
);

/**
 * Tools screen.
 */
function root_branch_starter_screen() {
	$log = get_transient( 'root_branch_starter_log' );
	delete_transient( 'root_branch_starter_log' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Root & Branch starter content', 'root-branch-starter' ); ?></h1>
		<p><?php esc_html_e( 'Creates the Home, About, Gallery, Media, Tour and Events pages, the Articles / Videos / Events posts and uploads the photographs to the Media Library. Existing pages and posts with the same slug are left untouched, so it is safe to run more than once.', 'root-branch-starter' ); ?></p>
		<?php if ( 'root-branch' !== get_template() ) : ?>
			<div class="notice notice-warning inline"><p><?php esc_html_e( 'Activate the Root & Branch theme first — page content is built from its patterns.', 'root-branch-starter' ); ?></p></div>
		<?php else : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="root_branch_starter_import">
				<?php wp_nonce_field( 'root_branch_starter_import' ); ?>
				<?php submit_button( __( 'Import content', 'root-branch-starter' ) ); ?>
			</form>
		<?php endif; ?>
		<?php if ( $log ) : ?>
			<h2><?php esc_html_e( 'Result', 'root-branch-starter' ); ?></h2>
			<pre style="background:#fff;padding:1em;max-height:400px;overflow:auto"><?php echo esc_html( implode( "\n", (array) $log ) ); ?></pre>
		<?php endif; ?>
	</div>
	<?php
}

add_action(
	'admin_post_root_branch_starter_import',
	static function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'root-branch-starter' ), 403 );
		}
		check_admin_referer( 'root_branch_starter_import' );
		$importer = new Root_Branch_Starter_Importer();
		$importer->run();
		set_transient( 'root_branch_starter_log', $importer->log, 5 * MINUTE_IN_SECONDS );
		wp_safe_redirect( admin_url( 'tools.php?page=root-branch-starter' ) );
		exit;
	}
);

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'root-branch import',
		static function () {
			$importer = new Root_Branch_Starter_Importer();
			$importer->run();
			foreach ( $importer->log as $line ) {
				WP_CLI::log( $line );
			}
			WP_CLI::success( 'Root & Branch content imported.' );
		}
	);
}
