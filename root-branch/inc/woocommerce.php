<?php
/**
 * WooCommerce integration.
 *
 * The store itself (products, prices, cart, checkout, accounts, orders,
 * payments) is native WooCommerce. This file only declares support. Custom
 * store templates go in templates/ using WooCommerce's block template names
 * (archive-product.html, taxonomy-product_cat.html, single-product.html,
 * page-cart.html, page-checkout.html, order-confirmation.html, …) and their
 * styles in assets/css/woocommerce.css, loaded from here only on store pages.
 *
 * @package RootBranch
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		add_theme_support( 'woocommerce' );
	}
);

// WooCommerce auto-inserts an account icon into the header; the design has only the cart.
add_filter(
	'hooked_block_types',
	static function ( $hooked ) {
		return array_values( array_diff( $hooked, array( 'woocommerce/customer-account' ) ) );
	}
);

add_action(
	'wp_enqueue_scripts',
	static function () {
		if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'is_woocommerce' ) ) {
			return;
		}
		$path = get_theme_file_path( 'assets/css/woocommerce.css' );
		if ( ! file_exists( $path ) ) {
			return;
		}
		if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
			wp_enqueue_style( 'root-branch-woocommerce', get_theme_file_uri( 'assets/css/woocommerce.css' ), array( 'root-branch-base' ), ROOT_BRANCH_VERSION . '.' . filemtime( $path ) );
		}
	}
);
