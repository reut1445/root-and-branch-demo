<?php
/**
 * Title: Sign-up pop-up
 * Slug: root-branch/signup-modal
 * Categories: root-branch-sections, call-to-action
 * Block Types: core/template-part/uncategorized
 * Inserter: no
 * Description: Content of the pop-up opened by the header "Sign up" button. Edit it in Appearance → Editor → Patterns → Template parts → Sign-up pop-up.
 *
 * @package RootBranch
 */

?>
<!-- wp:group {"className":"rb-modal__card","layout":{"type":"default"}} -->
<div class="wp-block-group rb-modal__card"><!-- wp:image <?php echo root_branch_image_attrs( 'gallery-24', array( 'className' => 'rb-modal__photo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> -->
<figure class="wp-block-image size-large rb-modal__photo"><?php echo root_branch_img( 'gallery-24', 'A volunteer harvesting olives from an ancient tree' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"rb-modal__body","layout":{"type":"default"}} -->
<div class="wp-block-group rb-modal__body"><!-- wp:paragraph {"className":"rb-modal__eyebrow"} -->
<p class="rb-modal__eyebrow">Join the next harvest</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"anchor":"rb-modal-title","className":"rb-modal__title"} -->
<h2 class="wp-block-heading rb-modal__title" id="rb-modal-title">Plant your roots <span class="rb-accent">in Israel</span></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"rb-modal__text"} -->
<p class="rb-modal__text">Every olive we gather together is a living bridge between Christians and Israel. Leave your details and we will save you a place.</p>
<!-- /wp:paragraph -->

<!-- wp:root-branch/signup-form {"buttonText":"COUNT ME IN →","listName":"Header pop-up"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
