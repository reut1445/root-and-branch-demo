<?php
/**
 * Title: Root & Branch in Action (photo collage)
 * Slug: root-branch/in-action
 * Categories: root-branch-sections, gallery
 * Keywords: gallery, collage, photos, action
 * Description: Scattered collage of twelve photos with a title and sign-up button. Swap photos from the gallery block's toolbar.
 * Viewport Width: 1920
 *
 * @package RootBranch
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"rb-in-action","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull rb-in-action"><!-- wp:gallery {"columns":3,"linkTo":"none","sizeSlug":"medium_large","align":"full","className":"is-style-rb-scatter"} -->
<figure class="wp-block-gallery alignfull has-nested-images columns-3 is-cropped is-style-rb-scatter">
<?php for ( $root_branch_i = 1; $root_branch_i <= 12; $root_branch_i++ ) : ?>
<!-- wp:image <?php echo root_branch_image_attrs( 'gallery-' . $root_branch_i, array( 'sizeSlug' => 'medium_large', 'linkDestination' => 'none' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> -->
<figure class="wp-block-image size-medium_large"><?php echo root_branch_img( 'gallery-' . $root_branch_i, 'Root & Branch volunteers in Israel' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
<!-- /wp:image -->
<?php endfor; ?>
</figure>
<!-- /wp:gallery -->

<!-- wp:heading {"textAlign":"center","className":"rb-in-action__title"} -->
<h2 class="wp-block-heading has-text-align-center rb-in-action__title">Root &amp; Branch <span class="rb-accent">in Action</span></h2>
<!-- /wp:heading -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"rb-button-lg"} -->
<div class="wp-block-button rb-button-lg"><a class="wp-block-button__link wp-element-button" href="#sign-up">SIGN UP NOW</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></section>
<!-- /wp:group -->
