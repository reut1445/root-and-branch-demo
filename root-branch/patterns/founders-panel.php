<?php
/**
 * Title: Founders panel
 * Slug: root-branch/founders-panel
 * Categories: root-branch-sections
 * Keywords: founders, about, panel, logo
 * Description: Logo badge over a rounded taupe panel with title, text and a tilted photo. Place directly under the page hero.
 * Viewport Width: 1920
 *
 * @package RootBranch
 */

?>
<!-- wp:group {"align":"full","className":"is-style-rb-badge","layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group alignfull is-style-rb-badge"><!-- wp:site-logo {"width":207,"shouldSyncIcon":false} /--></div>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"is-style-rb-panel rb-founders","backgroundColor":"taupe","textColor":"white","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull is-style-rb-panel rb-founders has-white-color has-taupe-background-color has-text-color has-background"><!-- wp:group {"className":"rb-founders__text","layout":{"type":"default"}} -->
<div class="wp-block-group rb-founders__text"><!-- wp:heading {"textColor":"white"} -->
<h2 class="wp-block-heading has-white-color has-text-color">Our Founders</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">Root &amp; Branch was born from a providential meeting between Iranian American Marziyeh Amirizadeh and Israeli American Jonathan Feldstein in March 2023. Root &amp; Branch emerged as a powerful symbol of unity and support for Israel.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:image <?php echo root_branch_image_attrs( 'founders', array( 'className' => 'rb-founders__photo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> -->
<figure class="wp-block-image size-large rb-founders__photo"><?php echo root_branch_img( 'founders', 'Marziyeh Amirizadeh and Jonathan Feldstein, founders of Root & Branch' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
<!-- /wp:image --></section>
<!-- /wp:group -->
