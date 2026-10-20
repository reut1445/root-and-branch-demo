<?php
/**
 * Title: Statement with photos
 * Slug: root-branch/statement
 * Categories: root-branch-sections, text
 * Keywords: quote, statement, signature
 * Description: Large statement between two tilted photos, with the olive branch and the Root & Branch signature.
 * Viewport Width: 1920
 *
 * @package RootBranch
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-rb-statement","layout":{"type":"constrained","contentSize":"1376px"}} -->
<section class="wp-block-group alignfull is-style-rb-statement"><!-- wp:paragraph {"align":"center","className":"rb-statement__text","fontFamily":"display","fontSize":"jumbo"} -->
<p class="has-text-align-center rb-statement__text has-display-font-family has-jumbo-font-size">From ancient roots, we grow together through faith, <span class="rb-accent">friendship,</span> and <span class="rb-accent">meaningful action.</span></p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"rb-statement__photos","layout":{"type":"default"}} -->
<div class="wp-block-group rb-statement__photos"><!-- wp:image <?php echo root_branch_image_attrs( 'statement-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> -->
<figure class="wp-block-image size-large"><?php echo root_branch_img( 'statement-left', 'An ancient olive tree' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
<!-- /wp:image -->

<!-- wp:image <?php echo root_branch_image_attrs( 'statement-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> -->
<figure class="wp-block-image size-large"><?php echo root_branch_img( 'statement-right', 'Freshly harvested olives' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
<!-- /wp:image --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"align":"center","className":"is-style-rb-signature"} -->
<p class="has-text-align-center is-style-rb-signature">Root &amp; Branch</p>
<!-- /wp:paragraph --></section>
<!-- /wp:group -->
