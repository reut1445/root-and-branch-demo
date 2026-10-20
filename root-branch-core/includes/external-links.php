<?php
/**
 * External links for posts.
 *
 * Media-coverage articles live on other websites. A post can carry an
 * "External link"; everywhere WordPress links to that post (cards, titles,
 * "Read more") the visitor goes straight to the article in a new tab.
 *
 * @package RootBranchCore
 */

defined( 'ABSPATH' ) || exit;

const ROOT_BRANCH_EXTERNAL_URL = 'rb_external_url';

add_action(
	'init',
	static function () {
		register_post_meta(
			'post',
			ROOT_BRANCH_EXTERNAL_URL,
			array(
				'type'              => 'string',
				'single'            => true,
				'default'           => '',
				'show_in_rest'      => array( 'schema' => array( 'format' => 'uri' ) ),
				'sanitize_callback' => 'esc_url_raw',
				'auth_callback'     => static function ( $allowed, $meta_key, $post_id ) {
					return current_user_can( 'edit_post', $post_id );
				},
			)
		);
	}
);

/**
 * External URL for a post, or ''.
 *
 * @param int|WP_Post $post Post.
 * @return string
 */
function root_branch_core_external_url( $post ) {
	$post = get_post( $post );
	if ( ! $post || 'post' !== $post->post_type ) {
		return '';
	}
	return (string) get_post_meta( $post->ID, ROOT_BRANCH_EXTERNAL_URL, true );
}

// Point permalinks at the external article (front end only; the editor keeps the real link).
add_filter(
	'post_link',
	static function ( $permalink, $post ) {
		if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return $permalink;
		}
		$external = root_branch_core_external_url( $post );
		return $external ? $external : $permalink;
	},
	10,
	2
);

// Open external articles in a new tab from cards and titles.
add_filter(
	'render_block',
	static function ( $html, $block, $instance ) {
		$targets = array( 'core/post-title', 'core/read-more', 'core/post-featured-image' );
		if ( ! in_array( $block['blockName'], $targets, true ) || empty( $instance->context['postId'] ) ) {
			return $html;
		}
		if ( ! root_branch_core_external_url( $instance->context['postId'] ) ) {
			return $html;
		}
		$tags = new WP_HTML_Tag_Processor( $html );
		while ( $tags->next_tag( 'a' ) ) {
			$tags->set_attribute( 'target', '_blank' );
			$tags->set_attribute( 'rel', 'noopener' );
		}
		return $tags->get_updated_html();
	},
	10,
	3
);

// Anyone landing on the local copy of an external article is sent on.
add_action(
	'template_redirect',
	static function () {
		if ( ! is_singular( 'post' ) || is_preview() ) {
			return;
		}
		$external = root_branch_core_external_url( get_queried_object_id() );
		if ( $external ) {
			wp_redirect( $external, 302, 'Root & Branch' ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- deliberate off-site link set by an editor.
			exit;
		}
	}
);

// Sidebar field in the post editor.
add_action(
	'enqueue_block_editor_assets',
	static function () {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && 'post' !== $screen->post_type ) {
			return;
		}
		wp_enqueue_script(
			'root-branch-external-link',
			ROOT_BRANCH_CORE_URL . 'assets/external-link-panel.js',
			array( 'wp-plugins', 'wp-editor', 'wp-components', 'wp-core-data', 'wp-data', 'wp-element', 'wp-i18n' ),
			ROOT_BRANCH_CORE_VERSION,
			true
		);
	}
);
