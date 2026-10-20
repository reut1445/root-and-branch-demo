<?php
/**
 * Title: Value rows
 * Slug: root-branch/value-rows
 * Categories: root-branch-sections
 * Keywords: values, rows, unity, features
 * Description: Full-width rows of title, text and shaded photo, divided by rules. Duplicate a row to add one.
 * Viewport Width: 1920
 *
 * @package RootBranch
 */

$root_branch_rows = array(
	array(
		'key'   => 'value-unity',
		'title' => 'Unity',
		'alt'   => 'Volunteers harvesting olives onto blue tarps',
		'text'  => 'Our project uniquely brings together Christians from all over the world and Israeli Jews, in a powerful demonstration of unity, symbolized by the ancient olive trees we harvest together.',
	),
	array(
		'key'   => 'value-connection',
		'title' => 'Connection',
		'alt'   => 'Root & Branch volunteers celebrating together',
		'text'  => 'Root &amp; Branch is the most exciting, new, and meaningful way to connect with Israel in a personal hands-on basis. Root &amp; Branch offers a unique vision, to bring Christians to harvest olives in Israel specifically in communities along the Gaza border that suffered so much from the horrific Hamas attack of October 7, 2023. We produce rich oil from these olives, and the proceeds from selling the oil and other products will be used to fund impactful social projects in and among the communities in which the olives are harvested, and which suffered so much devastation.',
	),
	array(
		'key'   => 'value-experience',
		'title' => 'Experience',
		'alt'   => 'Volunteers with crates of harvested olives',
		'text'  => 'More than just an agricultural pilgrimage, Root &amp; Branch connects participants with Israelis of all backgrounds including soldiers, at-risk youth and the programs that serve them, families of hostages and those who have suffered loss, heroes of October 7, national and local political leaders, and more – all leading to an intimate and informed experience of a lifetime.',
	),
	array(
		'key'   => 'value-partnership',
		'title' => 'Partnership',
		'alt'   => 'A crate of freshly harvested olives',
		'text'  => 'Root &amp; Branch was founded by Marziyeh Amirizadeh, an Iranian-born Christian, and Jonathan Feldstein, an American-born Orthodox Israeli Jew. Root &amp; Branch is a partnership and joint project of <a href="https://newpersia.org/" target="_blank" rel="noreferrer noopener">NewPersia.org</a> and the <a href="https://genesis123.co/" target="_blank" rel="noreferrer noopener">Genesis 123 Foundation</a>. This partnership is one of the keys not only to the program\'s uniqueness but also its success.',
	),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"rb-value-rows","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull rb-value-rows">
<?php foreach ( $root_branch_rows as $root_branch_row ) : ?>
<!-- wp:group {"className":"is-style-rb-value-row","layout":{"type":"default"}} -->
<div class="wp-block-group is-style-rb-value-row"><!-- wp:heading {"level":3,"textColor":"earth","fontSize":"xx-large"} -->
<h3 class="wp-block-heading has-earth-color has-text-color has-xx-large-font-size"><?php echo esc_html( $root_branch_row['title'] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"medium"} -->
<p class="has-medium-font-size"><?php echo wp_kses_post( $root_branch_row['text'] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:image <?php echo root_branch_image_attrs( $root_branch_row['key'], array( 'className' => 'is-style-rb-shade' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> -->
<figure class="wp-block-image size-large is-style-rb-shade"><?php echo root_branch_img( $root_branch_row['key'], $root_branch_row['alt'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
<!-- /wp:image --></div>
<!-- /wp:group -->
<?php endforeach; ?>
</section>
<!-- /wp:group -->
