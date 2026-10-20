<?php
/**
 * Front-end performance.
 *
 * @package RootBranch
 */

defined( 'ABSPATH' ) || exit;

/**
 * Preload the two fonts used above the fold so the hero does not re-flow.
 */
add_action(
	'wp_head',
	static function () {
		$poster = is_front_page() ? root_branch_media( 'hero-video-poster', '' ) : array( 'url' => '' );
		if ( $poster['url'] ) {
			printf( '<link rel="preload" href="%s" as="image" fetchpriority="high">' . "\n", esc_url( $poster['url'] ) );
		}
		foreach ( array( 'italiana-400', 'poppins-300' ) as $font ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( get_theme_file_uri( "assets/fonts/{$font}.woff2" ) )
			);
		}
	},
	1
);

/**
 * The hero image is the Largest Contentful Paint: never lazy-load it.
 */
add_filter(
	'render_block_core/cover',
	static function ( $html, $block ) {
		$class = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
		if ( false === strpos( $class, 'is-style-rb-hero' ) ) {
			return $html;
		}
		$tags = new WP_HTML_Tag_Processor( $html );
		if ( $tags->next_tag( array( 'class_name' => 'wp-block-cover__image-background' ) ) ) {
			$tags->set_attribute( 'loading', 'eager' );
			$tags->set_attribute( 'fetchpriority', 'high' );
			$tags->set_attribute( 'decoding', 'async' );
			return $tags->get_updated_html();
		}

		// Background video: show a still frame instantly, then play the loop.
		$tags = new WP_HTML_Tag_Processor( $html );
		if ( $tags->next_tag( array( 'class_name' => 'wp-block-cover__video-background' ) ) ) {
			$poster = root_branch_media( 'hero-video-poster', '' );
			if ( $poster['url'] ) {
				$tags->set_attribute( 'poster', $poster['url'] );
			}
			$tags->set_attribute( 'preload', 'auto' );
			$tags->set_attribute( 'aria-hidden', 'true' );
			$tags->set_attribute( 'disablepictureinpicture', true );
		}
		return $tags->get_updated_html();
	},
	10,
	2
);

// Emoji detection script and styles are not needed for this site.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
