<?php
/**
 * Title: Hero
 * Slug: root-branch/hero
 * Categories: root-branch-sections, banner
 * Keywords: hero, cover, banner, sign up
 * Description: Full-width photo hero with headline, subtitle and the notched sign-up button. Use once, at the top of a page.
 * Viewport Width: 1920
 *
 * @package RootBranch
 */

$root_branch_hero  = root_branch_media( 'hero' );
$root_branch_video = root_branch_media( 'hero-video', '' );
$root_branch_attrs = array(
	'dimRatio'           => 50,
	'overlayColor'       => 'black',
	'isUserOverlayColor' => true,
	'tagName'            => 'section',
	'align'              => 'full',
	'className'          => 'is-style-rb-hero',
	'layout'             => array( 'type' => 'constrained' ),
);
if ( $root_branch_video['id'] ) {
	$root_branch_attrs = array( 'url' => $root_branch_video['url'], 'id' => $root_branch_video['id'], 'backgroundType' => 'video' ) + $root_branch_attrs;
} else {
	$root_branch_attrs = array_filter( array( 'url' => $root_branch_hero['url'], 'id' => $root_branch_hero['id'] ) ) + $root_branch_attrs;
}
?>
<!-- wp:cover <?php echo wp_json_encode( $root_branch_attrs ); ?> -->
<section class="wp-block-cover alignfull is-style-rb-hero"><?php if ( $root_branch_video['id'] ) : ?><video class="wp-block-cover__video-background intrinsic-ignore" autoplay muted loop playsinline src="<?php echo esc_url( $root_branch_video['url'] ); ?>" data-object-fit="cover"></video><?php else : ?><img class="wp-block-cover__image-background<?php echo $root_branch_hero['id'] ? ' wp-image-' . (int) $root_branch_hero['id'] : ''; ?>" alt="" src="<?php echo esc_url( $root_branch_hero['url'] ); ?>" data-object-fit="cover"/><?php endif; ?><span aria-hidden="true" class="wp-block-cover__background has-black-background-color has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":1,"textColor":"white","fontSize":"display"} -->
<h1 class="wp-block-heading has-text-align-center has-white-color has-text-color has-display-font-size">A Meaningful Connection to Israel</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"white","fontSize":"x-large","className":"rb-hero__subtitle"} -->
<p class="has-text-align-center rb-hero__subtitle has-white-color has-text-color has-x-large-font-size">Root &amp; Branch: Christians Harvesting Olives in Israel</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"className":"is-style-rb-notch","layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons is-style-rb-notch"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#sign-up">SIGN UP NOW</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></section>
<!-- /wp:cover -->
