<?php
/**
 * Pattern categories.
 *
 * Patterns themselves are auto-registered from /patterns.
 *
 * @package RootBranch
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	static function () {
		$categories = array(
			'root-branch-sections' => __( 'Root & Branch · Sections', 'root-branch' ),
			'root-branch-cards'    => __( 'Root & Branch · Cards', 'root-branch' ),
			'root-branch-pages'    => __( 'Root & Branch · Full pages', 'root-branch' ),
		);
		foreach ( $categories as $slug => $label ) {
			register_block_pattern_category( $slug, array( 'label' => $label ) );
		}
	}
);
