<?php
/**
 * Title: Page hero
 * Slug: root-branch/page-hero
 * Categories: root-branch-sections, banner
 * Keywords: hero, page title, banner, cover
 * Description: Shorter photo hero with the page title, for inner pages. Use once, at the top of a page.
 * Viewport Width: 1920
 *
 * @package RootBranch
 */

$root_branch_hero = root_branch_media( 'hero' );
?>
<!-- wp:cover <?php echo wp_json_encode( array_filter( array( 'url' => $root_branch_hero['url'], 'id' => $root_branch_hero['id'], 'dimRatio' => 50, 'overlayColor' => 'black', 'isUserOverlayColor' => true, 'tagName' => 'section', 'align' => 'full', 'className' => 'is-style-rb-hero rb-hero--page', 'layout' => array( 'type' => 'constrained' ) ) ) ); ?> -->
<section class="wp-block-cover alignfull is-style-rb-hero rb-hero--page"><img class="wp-block-cover__image-background<?php echo $root_branch_hero['id'] ? ' wp-image-' . (int) $root_branch_hero['id'] : ''; ?>" alt="" src="<?php echo esc_url( $root_branch_hero['url'] ); ?>" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-black-background-color has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":1,"textColor":"white"} -->
<h1 class="wp-block-heading has-text-align-center has-white-color has-text-color">Page Title</h1>
<!-- /wp:heading --></div></section>
<!-- /wp:cover -->
