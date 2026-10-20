<?php
/**
 * Image helpers for block patterns.
 *
 * Patterns refer to photographs by a stable key (e.g. "hero"). The starter
 * content plugin uploads the photographs to the Media Library and stores a
 * key => attachment ID map in the `root_branch_media_map` option, so patterns
 * inserted later use real attachments (with srcset). Without that map the
 * patterns fall back to the neutral placeholder from the design system.
 *
 * @package RootBranch
 */

defined( 'ABSPATH' ) || exit;

/**
 * Resolve a pattern image key.
 *
 * @param string $key      Image key.
 * @param string $fallback Theme-relative file used when no attachment is mapped.
 * @param string $size     Image size for the src (srcset is added by WordPress).
 * @return array{id:int,url:string,alt:string}
 */
function root_branch_media( $key, $fallback = 'assets/images/placeholder.svg', $size = 'large' ) {
	$map = get_option( 'root_branch_media_map', array() );
	$id  = isset( $map[ $key ] ) ? (int) $map[ $key ] : 0;

	if ( $id && ! wp_attachment_is_image( $id ) && get_post( $id ) ) {
		return array(
			'id'  => $id,
			'url' => (string) wp_get_attachment_url( $id ),
			'alt' => '',
		);
	}

	if ( $id && wp_attachment_is_image( $id ) ) {
		return array(
			'id'  => $id,
			'url' => (string) wp_get_attachment_image_url( $id, $size ),
			'alt' => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ),
		);
	}

	return array(
		'id'  => 0,
		'url' => $fallback ? get_theme_file_uri( $fallback ) : '',
		'alt' => '',
	);
}

/**
 * Attributes for an image block, JSON-encoded for block comment delimiters.
 *
 * @param string $key   Image key.
 * @param array  $extra Additional block attributes.
 * @return string
 */
function root_branch_image_attrs( $key, $extra = array() ) {
	$media = root_branch_media( $key );
	$attrs = array_merge(
		array_filter(
			array(
				'id'       => $media['id'],
				'sizeSlug' => 'large',
			)
		),
		$extra
	);
	return wp_json_encode( $attrs );
}

/**
 * Markup for an <img> inside a block, matching what the editor saves.
 *
 * @param string $key      Image key.
 * @param string $alt      Fallback alt text when the attachment has none.
 * @param string $fallback Theme-relative fallback file.
 * @return string
 */
function root_branch_img( $key, $alt = '', $fallback = 'assets/images/placeholder.svg' ) {
	$media = root_branch_media( $key, $fallback );
	$class = $media['id'] ? sprintf( ' class="wp-image-%d"', $media['id'] ) : '';
	$alt   = $media['alt'] ? $media['alt'] : $alt;
	return sprintf( '<img src="%s" alt="%s"%s/>', esc_url( $media['url'] ), esc_attr( $alt ), $class );
}
