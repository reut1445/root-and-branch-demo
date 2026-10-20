<?php
/**
 * Title: Footer
 * Slug: root-branch/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Inserter: no
 *
 * @package RootBranch
 */

$root_branch_menu = array(
	array( 'Home', '/' ),
	array( 'About', '/about/' ),
	array( 'Mission', '/#mission' ),
	array( 'Gallery', '/gallery/' ),
	array( 'Media', '/media/' ),
	array( 'Tour', '/tour/' ),
	array( 'Events', '/events/' ),
	array( 'Sign Up', '/#sign-up' ),
);
$root_branch_blog = array(
	array( 'Media Coverage', '/media/' ),
	array( 'Articles', '/category/articles/' ),
	array( 'Videos', '/category/videos/' ),
	array( 'Events', '/events/' ),
);
$root_branch_product = array(
	array( 'Shop', '/shop/' ),
	array( 'Donate', 'https://links.rootandbranchisrael.com/widget/form/zYs85trJa9IZuCzAqTV5?notrack=true' ),
);

/**
 * Print a footer navigation column.
 *
 * @param string $title Column title.
 * @param array  $links Label/URL pairs.
 */
$root_branch_column = static function ( $title, $links ) {
	?>
	<!-- wp:group {"className":"rb-footer__column","layout":{"type":"flex","orientation":"vertical"}} -->
	<div class="wp-block-group rb-footer__column"><!-- wp:heading {"level":2,"className":"rb-footer__title"} -->
	<h2 class="wp-block-heading rb-footer__title"><?php echo esc_html( $title ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:navigation {"overlayMenu":"never","className":"rb-footer__nav","layout":{"type":"flex","orientation":"vertical"}} -->
	<?php foreach ( $links as $link ) : ?>
	<!-- wp:navigation-link <?php echo wp_json_encode( array_filter( array( 'label' => $link[0], 'url' => $link[1], 'kind' => 'custom', 'isTopLevelLink' => true, 'opensInNewTab' => 0 === strpos( $link[1], 'http' ) ) ) ); ?> /-->
	<?php endforeach; ?>
	<!-- /wp:navigation --></div>
	<!-- /wp:group -->
	<?php
};
?>
<!-- wp:group {"tagName":"div","align":"full","className":"rb-footer","backgroundColor":"sand","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull rb-footer has-sand-background-color has-background"><!-- wp:group {"className":"rb-footer__brand","layout":{"type":"default"}} -->
<div class="wp-block-group rb-footer__brand"><!-- wp:site-logo {"width":358,"shouldSyncIcon":false,"className":"rb-footer__logo"} /-->

<!-- wp:paragraph {"className":"rb-footer__about"} -->
<p class="rb-footer__about">A project of <a href="https://genesis123.co/" target="_blank" rel="noreferrer noopener">Genesis 123 Foundation</a> and <a href="https://newpersia.org/" target="_blank" rel="noreferrer noopener">NewPersia.org</a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"rb-footer__email"} -->
<p class="rb-footer__email"><a href="mailto:rootandbranchisrael@gmail.com">rootandbranchisrael@gmail.com</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"rb-footer__menus","layout":{"type":"default"}} -->
<div class="wp-block-group rb-footer__menus">
<?php
$root_branch_column( __( 'Quick Links', 'root-branch' ), $root_branch_menu );
$root_branch_column( __( 'Blog', 'root-branch' ), $root_branch_blog );
$root_branch_column( __( 'Our Product', 'root-branch' ), $root_branch_product );
?>
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"rb-footer__partners","layout":{"type":"default"}} -->
<div class="wp-block-group rb-footer__partners"><!-- wp:image <?php echo root_branch_image_attrs( 'logo-new-persia', array( 'linkDestination' => 'custom', 'className' => 'rb-footer__partner rb-footer__partner--persia' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> -->
<figure class="wp-block-image size-large rb-footer__partner rb-footer__partner--persia"><a href="https://newpersia.org/" target="_blank" rel="noreferrer noopener"><?php echo root_branch_img( 'logo-new-persia', 'New Persia — Jeremiah 49:38-39', 'assets/images/partners/new-persia.webp' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></figure>
<!-- /wp:image -->

<!-- wp:image <?php echo root_branch_image_attrs( 'logo-genesis-123', array( 'linkDestination' => 'custom', 'className' => 'rb-footer__partner rb-footer__partner--genesis' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> -->
<figure class="wp-block-image size-large rb-footer__partner rb-footer__partner--genesis"><a href="https://genesis123.co/" target="_blank" rel="noreferrer noopener"><?php echo root_branch_img( 'logo-genesis-123', 'Genesis 123 Foundation', 'assets/images/partners/genesis-123.webp' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></figure>
<!-- /wp:image --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"rb-footer-bottom","layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"}} -->
<div class="wp-block-group alignfull rb-footer-bottom"><!-- wp:paragraph -->
<p>Designed by Reut Kishon</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>© Copyright 2026 - <a href="/">Root &amp; Branch</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
