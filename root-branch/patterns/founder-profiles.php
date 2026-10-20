<?php
/**
 * Title: Founder profiles
 * Slug: root-branch/founder-profiles
 * Categories: root-branch-sections
 * Keywords: founder, team, profile, biography
 * Description: Two staggered founder profiles (the second mirrored), each with name, portrait, biography and a link.
 * Viewport Width: 1920
 *
 * @package RootBranch
 */

$root_branch_founders = array(
	array(
		'style'    => 'is-style-rb-founder',
		'name'     => 'Marziyeh Amirizadeh',
		'image'    => 'founder-marziyeh',
		'alt'      => 'Marziyeh Amirizadeh',
		'lead'     => 'Marziyeh (Marzi) Amirizadeh is an Iranian American who immigrated to the US after being sentenced to death by hanging in Iran for the \'crime\' of converting to Christianity.',
		'text'     => 'Marzi is a published author, public speaker, and columnist. Her first book, \'Captive in Iran,\' recounts her capture and imprisonment. Her second book, \'A Love Journey with God,\' shares how Marzi found God, the struggles she experienced as a Christian woman in an Islamic Country, and how God can turn any situation into triumph. (<a href="https://www.marzisjourney.com/" target="_blank" rel="noreferrer noopener">www.marzisjourney.com</a>)',
		'more'     => 'Marzi also is the founder and president of NEW PERSIA an organization whose mission is to be the voice of persecuted Christians and oppressed women under Islam, to Preserve democracy that is being threaten in the West by extremist Islam, expose the lies of the Iranian Islamic regime, and restore the relationships between Persians, Jews, and Christians. (<a href="https://www.newpersia.org/" target="_blank" rel="noreferrer noopener">www.newpersia.org</a>)',
		'more_url' => 'https://www.marzisjourney.com/',
	),
	array(
		'style'    => 'is-style-rb-founder-mirror',
		'name'     => 'Jonathan Feldstein',
		'image'    => 'founder-jonathan',
		'alt'      => 'Jonathan Feldstein',
		'lead'     => 'Jonathan Feldstein is president of the Genesis 123 Foundation whose mission is to build bridges between Jews and Christians and Christians with Israel. He was born and educated in the U.S. and immigrated to Israel in 2004. He is married and the father of six, and grandfather of four (so far). (<a href="https://www.genesis123.co/" target="_blank" rel="noreferrer noopener">www.genesis123.co</a>)',
		'text'     => 'Since the October 7, 2023 Hamas massacre in Israel, he has written hundreds of articles, appeared in numerous media interviews, hosted dozens of briefings, and used his voice to further the mission of building bridges between Jews and Christians through multiple global prayer events. Jonathan is also host of the \'Inspiration from Zion\' podcast.',
		'more'     => 'In 2023 he published the highly acclaimed book, \'Israel the Miracle\', including essays by 75 Christian leaders from all over the world writing about why Israel is significant to them. (<a href="https://www.israelthemiracle.com/" target="_blank" rel="noreferrer noopener">www.israelthemiracle.com</a>)',
		'more_url' => 'https://www.genesis123.co/',
	),
);

foreach ( $root_branch_founders as $root_branch_f ) :
	?>
<!-- wp:group {"tagName":"section","align":"full","className":"<?php echo esc_attr( $root_branch_f['style'] ); ?>","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull <?php echo esc_attr( $root_branch_f['style'] ); ?>"><!-- wp:group {"className":"rb-founder__portrait","layout":{"type":"default"}} -->
<div class="wp-block-group rb-founder__portrait"><!-- wp:heading {"textAlign":"center","fontSize":"jumbo"} -->
<h2 class="wp-block-heading has-text-align-center has-jumbo-font-size"><?php echo esc_html( $root_branch_f['name'] ); ?></h2>
<!-- /wp:heading -->

<!-- wp:image <?php echo root_branch_image_attrs( $root_branch_f['image'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?> -->
<figure class="wp-block-image size-large"><?php echo root_branch_img( $root_branch_f['image'], $root_branch_f['alt'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
<!-- /wp:image --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"rb-founder__bio","layout":{"type":"default"}} -->
<div class="wp-block-group rb-founder__bio"><!-- wp:paragraph {"className":"rb-founder__lead","fontSize":"lead-strong"} -->
<p class="rb-founder__lead has-lead-strong-font-size"><?php echo wp_kses_post( $root_branch_f['lead'] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"rb-muted"} -->
<p class="rb-muted"><?php echo wp_kses_post( $root_branch_f['text'] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"rb-founder__more","layout":{"type":"default"}} -->
<div class="wp-block-group rb-founder__more"><!-- wp:paragraph {"className":"rb-muted"} -->
<p class="rb-muted"><?php echo wp_kses_post( $root_branch_f['more'] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"rb-button-md"} -->
<div class="wp-block-button rb-button-md"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $root_branch_f['more_url'] ); ?>" target="_blank" rel="noreferrer noopener">READ MORE →</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
	<?php
endforeach;
