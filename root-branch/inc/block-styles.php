<?php
/**
 * Block style variations.
 *
 * These are the design-system choices offered to editors in the block sidebar
 * ("Styles"). The CSS for each lives in assets/css/blocks/{block}.css.
 *
 * @package RootBranch
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	static function () {
		$styles = array(
			'core/cover'   => array(
				'rb-hero' => __( 'Hero (top of page)', 'root-branch' ),
			),
			'core/buttons' => array(
				'rb-notch' => __( 'Hero notch', 'root-branch' ),
			),
			'core/button'  => array(
				'rb-sand'  => __( 'Sand pill', 'root-branch' ),
				'rb-block' => __( 'Card button', 'root-branch' ),
			),
			'core/heading' => array(
				'rb-ornament'        => __( 'Olive branch above', 'root-branch' ),
				'rb-ornament-offset' => __( 'Olive branch above (offset)', 'root-branch' ),
			),
			'core/paragraph' => array(
				'rb-signature' => __( 'Signature with olive branch', 'root-branch' ),
			),
			'core/group'   => array(
				'rb-value-row'      => __( 'Value row', 'root-branch' ),
				'rb-mission-card'   => __( 'Mission card', 'root-branch' ),
				'rb-card'           => __( 'Media card', 'root-branch' ),
				'rb-panel'          => __( 'Rounded panel', 'root-branch' ),
				'rb-founder'        => __( 'Founder profile', 'root-branch' ),
				'rb-founder-mirror' => __( 'Founder profile (mirrored)', 'root-branch' ),
				'rb-timeline'       => __( 'Timeline', 'root-branch' ),
				'rb-timeline-item'  => __( 'Timeline item', 'root-branch' ),
				'rb-statement'      => __( 'Statement with photos', 'root-branch' ),
				'rb-badge'          => __( 'Logo badge', 'root-branch' ),
			),
			'core/gallery' => array(
				'rb-scatter' => __( 'Scattered collage', 'root-branch' ),
				'rb-masonry' => __( 'Masonry', 'root-branch' ),
			),
			'core/image'   => array(
				'rb-shade' => __( 'Shaded photo', 'root-branch' ),
			),
			'core/list'    => array(
				'rb-olive' => __( 'Olive bullets', 'root-branch' ),
			),
			'core/read-more' => array(
				'rb-button' => __( 'Button', 'root-branch' ),
			),
		);

		foreach ( $styles as $block => $variations ) {
			foreach ( $variations as $name => $label ) {
				register_block_style(
					$block,
					array(
						'name'  => $name,
						'label' => $label,
					)
				);
			}
		}
	}
);
