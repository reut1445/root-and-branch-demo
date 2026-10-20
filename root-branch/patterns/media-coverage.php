<?php
/**
 * Title: Media coverage (latest articles)
 * Slug: root-branch/media-coverage
 * Categories: root-branch-sections, query
 * Keywords: media, articles, press, blog, posts
 * Description: The four latest posts in the "Articles" category as cards, with a link to the full media page. Add articles as posts — they appear here automatically.
 * Viewport Width: 1920
 *
 * @package RootBranch
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"rb-media","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull rb-media"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Media Coverage</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"rb-subtitle","fontSize":"large"} -->
<p class="has-text-align-center rb-subtitle has-large-font-size">Articles</p>
<!-- /wp:paragraph -->

<!-- wp:query <?php echo root_branch_query_attrs( 'articles', 4, 11 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> -->
<div class="wp-block-query alignwide rb-cards"><!-- wp:post-template {"className":"rb-card-grid","layout":{"type":"default"}} -->
<?php root_branch_card_markup( __( 'ARTICLE', 'root-branch' ) ); ?>
<!-- /wp:post-template --></div>
<!-- /wp:query -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"rb-button-lg"} -->
<div class="wp-block-button rb-button-lg"><a class="wp-block-button__link wp-element-button" href="/media/">MOVE TO BLOG →</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></section>
<!-- /wp:group -->
