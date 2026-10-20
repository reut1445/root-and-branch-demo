<?php
/**
 * Title: Mission & objectives
 * Slug: root-branch/mission
 * Categories: root-branch-sections, root-branch-cards
 * Keywords: mission, objectives, cards
 * Description: Section title with five staggered mission cards (forest and sand alternate). Duplicate or remove cards freely.
 * Viewport Width: 1920
 *
 * @package RootBranch
 */

$root_branch_cards = array(
	array( 'Experiencing Prophesy', 'Root &amp; Branch connects you with Biblical prophecies being realized in our day as God promised that when the Jewish people would return to the Land, it would flourish again, and that gentiles would come to serve alongside the Jewish people.' ),
	array( 'Agricultural Pilgrimage', 'Join us for a transformative experience combining agricultural service with spiritual fellowship, as we support communities along the Gaza border through active participation.' ),
	array( 'Spiritual Connection', 'Root &amp; Branch embodies the powerful connection between Christians and Jews, working together to support Israel through meaningful action and presence.' ),
	array( 'Tangible Support', 'Through olive harvesting, we provide vital economic support to Israeli communities while creating lasting spiritual bonds. Each olive represents our commitment to Israel\'s flourishing.' ),
	array( 'Building Relationships', 'More than a simple tour of the Holy Land, Root &amp; Branch has a powerful vision to create lasting partnerships and relations between Christians worldwide and Israeli Jews and communities throughout Israel.' ),
);
?>
<!-- wp:group {"tagName":"section","anchor":"mission","align":"full","className":"rb-mission","layout":{"type":"constrained"}} -->
<section id="mission" class="wp-block-group alignfull rb-mission"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Our Mission <span class="rb-amp">&amp;</span> Objectives</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"rb-lead","fontSize":"large"} -->
<p class="has-text-align-center rb-lead has-large-font-size">Showing Unconditional Love to Israel and the Jewish People</p>
<!-- /wp:paragraph -->

<!-- wp:group {"align":"wide","className":"rb-mission__cards","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide rb-mission__cards">
<?php
foreach ( $root_branch_cards as $root_branch_i => $root_branch_card ) :
	$root_branch_forest = 0 === $root_branch_i % 2;
	$root_branch_attrs  = $root_branch_forest
		? array( 'className' => 'is-style-rb-mission-card', 'backgroundColor' => 'forest', 'textColor' => 'white', 'layout' => array( 'type' => 'default' ) )
		: array( 'className' => 'is-style-rb-mission-card', 'backgroundColor' => 'sand', 'textColor' => 'black', 'layout' => array( 'type' => 'default' ) );
	$root_branch_class  = $root_branch_forest
		? 'has-white-color has-forest-background-color has-text-color has-background'
		: 'has-black-color has-sand-background-color has-text-color has-background';
	?>
<!-- wp:group <?php echo wp_json_encode( $root_branch_attrs ); ?> -->
<div class="wp-block-group is-style-rb-mission-card <?php echo esc_attr( $root_branch_class ); ?>"><!-- wp:heading {"textAlign":"center","level":3,"className":"rb-mission-card__title"} -->
<h3 class="wp-block-heading has-text-align-center rb-mission-card__title"><?php echo esc_html( $root_branch_card[0] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php echo wp_kses_post( $root_branch_card[1] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
<?php endforeach; ?>
</div>
<!-- /wp:group --></section>
<!-- /wp:group -->
