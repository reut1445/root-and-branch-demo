<?php
/**
 * Sign-up pop-up.
 *
 * The content is the "Sign-up pop-up" template part (editable in the Site
 * Editor). It is printed once per page inside a native <dialog>, so it works
 * on every template, including WooCommerce's. Links or buttons with the class
 * "rb-open-signup" open it (assets/js/header.js); without JavaScript they
 * simply follow their link to the sign-up section.
 *
 * @package RootBranch
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_footer',
	static function () {
		ob_start();
		block_template_part( 'signup-modal' );
		$content = trim( ob_get_clean() );
		if ( '' === $content ) {
			return;
		}
		?>
<dialog id="join" class="rb-modal" aria-labelledby="rb-modal-title">
	<form method="dialog" class="rb-modal__close-form"><button class="rb-modal__close" type="submit" aria-label="<?php esc_attr_e( 'Close', 'root-branch' ); ?>"><span aria-hidden="true">×</span></button></form>
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- rendered blocks. ?>
</dialog>
		<?php
	},
	5
);
