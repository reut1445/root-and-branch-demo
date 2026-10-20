<?php
/**
 * Stylesheets and scripts.
 *
 * base.css is the only global stylesheet (header, footer, primitives).
 * Everything else is attached to the block it styles through
 * wp_enqueue_block_style(), so it is printed only on pages that use that block.
 *
 * @package RootBranch
 */

defined( 'ABSPATH' ) || exit;

/**
 * Block stylesheets: assets/css/blocks/{namespace}-{block}.css => block name.
 *
 * @return array<string, string>
 */
function root_branch_block_stylesheets() {
	return array(
		'core-button'       => 'core/button',
		'core-cover'        => 'core/cover',
		'core-gallery'      => 'core/gallery',
		'core-group'        => 'core/group',
		'core-heading'      => 'core/heading',
		'core-image'        => 'core/image',
		'core-list'         => 'core/list',
		'core-navigation'   => 'core/navigation',
		'core-post-template' => 'core/post-template',
		'core-read-more'    => 'core/read-more',
		'root-branch-signup-form' => 'root-branch/signup-form',
	);
}

add_action(
	'init',
	static function () {
		foreach ( root_branch_block_stylesheets() as $file => $block ) {
			$path = get_theme_file_path( "assets/css/blocks/{$file}.css" );
			if ( ! file_exists( $path ) ) {
				continue;
			}
			wp_enqueue_block_style(
				$block,
				array(
					'handle' => "root-branch-{$file}",
					'src'    => get_theme_file_uri( "assets/css/blocks/{$file}.css" ),
					'path'   => $path,
					'ver'    => ROOT_BRANCH_VERSION . '.' . filemtime( $path ),
				)
			);
		}
	}
);

add_action(
	'wp_enqueue_scripts',
	static function () {
		$path = get_theme_file_path( 'assets/css/base.css' );
		wp_enqueue_style( 'root-branch-base', get_theme_file_uri( 'assets/css/base.css' ), array(), ROOT_BRANCH_VERSION . '.' . filemtime( $path ) );

		// Motion: front end only (never in the editor).
		$motion_css = get_theme_file_path( 'assets/css/motion.css' );
		$motion_js  = get_theme_file_path( 'assets/js/motion.js' );
		wp_enqueue_style( 'root-branch-motion', get_theme_file_uri( 'assets/css/motion.css' ), array( 'root-branch-base' ), ROOT_BRANCH_VERSION . '.' . filemtime( $motion_css ) );
		$vendor = array( 'in_footer' => true, 'strategy' => 'defer' );
		wp_register_script( 'gsap', get_theme_file_uri( 'assets/vendor/gsap/gsap.min.js' ), array(), '3.13.0', $vendor );
		wp_register_script( 'gsap-scrolltrigger', get_theme_file_uri( 'assets/vendor/gsap/ScrollTrigger.min.js' ), array( 'gsap' ), '3.13.0', $vendor );
		wp_register_script( 'lenis', get_theme_file_uri( 'assets/vendor/lenis/lenis.min.js' ), array(), '1.1.20', $vendor );
		wp_enqueue_style( 'lenis', get_theme_file_uri( 'assets/vendor/lenis/lenis.css' ), array(), '1.1.20' );
		wp_enqueue_script(
			'root-branch-smooth',
			get_theme_file_uri( 'assets/js/smooth.js' ),
			array( 'lenis', 'gsap-scrolltrigger' ),
			ROOT_BRANCH_VERSION . '.' . filemtime( get_theme_file_path( 'assets/js/smooth.js' ) ),
			$vendor
		);
		wp_enqueue_script(
			'root-branch-header',
			get_theme_file_uri( 'assets/js/header.js' ),
			array(),
			ROOT_BRANCH_VERSION . '.' . filemtime( get_theme_file_path( 'assets/js/header.js' ) ),
			$vendor
		);
		wp_enqueue_script(
			'root-branch-motion',
			get_theme_file_uri( 'assets/js/motion.js' ),
			array( 'gsap-scrolltrigger' ),
			ROOT_BRANCH_VERSION . '.' . filemtime( $motion_js ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
);

/**
 * Arm the reveal animations before first paint, so content never flashes
 * in and out. If motion.js does not run within 3s, everything is shown.
 * Visitors who prefer reduced motion are never armed.
 */
add_action(
	'wp_head',
	static function () {
		echo "<script>(function(d){if(!window.matchMedia||matchMedia('(prefers-reduced-motion: reduce)').matches)return;d.classList.add('rb-motion');setTimeout(function(){if(!window.rbMotionReady)d.classList.remove('rb-motion');},3000);})(document.documentElement);</script>\n";
	},
	2
);

add_action(
	'after_setup_theme',
	static function () {
		$styles = array( 'assets/css/base.css', 'assets/css/editor.css' );
		foreach ( array_keys( root_branch_block_stylesheets() ) as $file ) {
			$styles[] = "assets/css/blocks/{$file}.css";
		}
		add_editor_style( $styles );
	}
);
