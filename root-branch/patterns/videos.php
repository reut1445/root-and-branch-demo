<?php
/**
 * Title: Videos (latest)
 * Slug: root-branch/videos
 * Categories: root-branch-sections, query
 * Keywords: videos, media, posts
 * Description: The four latest posts in the "Videos" category as cards. Add a video as a post in that category — it appears here automatically.
 * Viewport Width: 1920
 *
 * @package RootBranch
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"rb-videos","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull rb-videos"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Videos</h2>
<!-- /wp:heading -->

<!-- wp:query <?php echo root_branch_query_attrs( 'videos', 4, 12 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> -->
<div class="wp-block-query alignwide rb-cards"><!-- wp:post-template {"className":"rb-card-grid","layout":{"type":"default"}} -->
<?php root_branch_card_markup( __( 'VIDEO', 'root-branch' ), true, __( 'WATCH VIDEO →', 'root-branch' ) ); ?>
<!-- /wp:post-template --></div>
<!-- /wp:query --></section>
<!-- /wp:group -->
