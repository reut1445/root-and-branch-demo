<?php
/**
 * Sign-up form block — server render.
 *
 * Works without JavaScript (posts to admin-post.php and redirects back);
 * view.js upgrades it to an in-place submission.
 *
 * @package RootBranchCore
 *
 * @var array    $attributes Block attributes.
 * @var WP_Block $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

$rb_uid     = wp_unique_id( 'rb-signup-' );
$rb_status  = isset( $_GET['rb-signup'] ) ? sanitize_key( wp_unslash( $_GET['rb-signup'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
$rb_message = '';
if ( 'success' === $rb_status ) {
	$rb_message = $attributes['successMessage'];
} elseif ( $rb_status ) {
	$rb_message = root_branch_core_signup_error_message( $rb_status );
}

$rb_fields = array(
	array(
		'name'         => 'rb_name',
		'type'         => 'text',
		'label'        => $attributes['nameLabel'],
		'autocomplete' => 'name',
		'required'     => true,
	),
	array(
		'name'         => 'rb_email',
		'type'         => 'email',
		'label'        => $attributes['emailLabel'],
		'autocomplete' => 'email',
		'required'     => true,
	),
);
if ( ! empty( $attributes['showPhone'] ) ) {
	$rb_fields[] = array(
		'name'         => 'rb_phone',
		'type'         => 'tel',
		'label'        => $attributes['phoneLabel'],
		'autocomplete' => 'tel',
		'required'     => false,
	);
}

$rb_wrapper = get_block_wrapper_attributes( array( 'class' => 'rb-signup' ) );
?>
<form <?php echo $rb_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?> method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-rb-signup data-success="<?php echo esc_attr( $attributes['successMessage'] ); ?>" data-error="<?php echo esc_attr( root_branch_core_signup_error_message( 'failed' ) ); ?>">
	<input type="hidden" name="action" value="root_branch_signup">
	<input type="hidden" name="rb_list" value="<?php echo esc_attr( $attributes['listName'] ); ?>">
	<input type="hidden" name="rb_ts" value="<?php echo esc_attr( (string) time() ); ?>">
	<?php if ( is_user_logged_in() ) : ?>
		<?php wp_nonce_field( 'root_branch_signup', 'rb_nonce' ); ?>
	<?php endif; ?>
	<div class="rb-signup__trap" aria-hidden="true">
		<label for="<?php echo esc_attr( $rb_uid ); ?>-website"><?php esc_html_e( 'Leave this field empty', 'root-branch-core' ); ?></label>
		<input type="text" id="<?php echo esc_attr( $rb_uid ); ?>-website" name="rb_website" tabindex="-1" autocomplete="off">
	</div>
	<?php foreach ( $rb_fields as $rb_field ) : ?>
		<div class="rb-signup__field">
			<input
				class="rb-signup__input"
				type="<?php echo esc_attr( $rb_field['type'] ); ?>"
				id="<?php echo esc_attr( $rb_uid . '-' . $rb_field['name'] ); ?>"
				name="<?php echo esc_attr( $rb_field['name'] ); ?>"
				autocomplete="<?php echo esc_attr( $rb_field['autocomplete'] ); ?>"
				placeholder=" "
				<?php echo $rb_field['required'] ? 'required aria-required="true"' : ''; ?>
			>
			<label class="rb-signup__label" for="<?php echo esc_attr( $rb_uid . '-' . $rb_field['name'] ); ?>"><?php echo esc_html( $rb_field['label'] ); ?></label>
		</div>
	<?php endforeach; ?>
	<button type="submit" class="rb-signup__submit wp-element-button"><?php echo esc_html( $attributes['buttonText'] ); ?></button>
	<p class="rb-signup__status<?php echo 'success' === $rb_status ? ' is-success' : ( $rb_status ? ' is-error' : '' ); ?>" role="status" aria-live="polite"><?php echo esc_html( $rb_message ); ?></p>
</form>
