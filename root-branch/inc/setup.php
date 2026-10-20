<?php
/**
 * Theme supports.
 *
 * @package RootBranch
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		load_theme_textdomain( 'root-branch', get_theme_file_path( 'languages' ) );

		add_theme_support( 'editor-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );

		// The theme ships its own patterns; core's remote/bundled ones do not follow the design system.
		remove_theme_support( 'core-block-patterns' );
	}
);

// Do not pull in the remote pattern directory either.
add_filter( 'should_load_remote_block_patterns', '__return_false' );
