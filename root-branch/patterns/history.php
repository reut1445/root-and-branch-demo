<?php
/**
 * Title: Historical achievement
 * Slug: root-branch/history
 * Categories: root-branch-sections
 * Keywords: history, impact, achievement
 * Description: Title, subtitle, paragraph and a large rounded photo.
 * Viewport Width: 1920
 *
 * @package RootBranch
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"rb-history","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull rb-history"><!-- wp:heading {"textAlign":"center","fontSize":"jumbo"} -->
<h2 class="wp-block-heading has-text-align-center has-jumbo-font-size">Historical Achievement <span class="rb-accent">and Impact</span></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"rb-muted","fontSize":"large","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center rb-muted has-large-font-size" style="font-weight:700">Building Bridges Through Faith and Action</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","className":"rb-muted"} -->
<p class="has-text-align-center rb-muted">In November 2024, the inaugural Root &amp; Branch group of Christian volunteers from across United States successfully harvested over 1,000 kilos of olives, demonstrating tangible support for communities affected by conflict. We harvested hundreds of year old trees in the Tel Ashkelon National Park, alongside archeological ruins thousands of years old. In addition we harvested in Sderot, the Israeli city closest to the Gaza border, and agricultural communities that were devastated by the October 2023 massacre.</p>
<!-- /wp:paragraph -->

<!-- wp:image <?php echo root_branch_image_attrs( 'history', array( 'align' => 'wide', 'className' => 'rb-radius-hero' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> -->
<figure class="wp-block-image alignwide size-large rb-radius-hero"><?php echo root_branch_img( 'history', 'Root & Branch volunteers at an ancient site in Israel' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
<!-- /wp:image --></section>
<!-- /wp:group -->
