<?php
/**
 * Title: Timeline
 * Slug: root-branch/timeline
 * Categories: root-branch-sections
 * Keywords: timeline, milestones, itinerary, steps
 * Description: Vertical line with milestones — title on the left, text and photo on the right. Duplicate an item to add a milestone; remove the photo for text-only items.
 * Viewport Width: 1920
 *
 * @package RootBranch
 */

$root_branch_items = array(
	array( 'Divine Affirmation', 'The project\'s first harvest day was marked by two rainbows - symbolizing God\'s continued covenant, and His blessing on this mission. It was also the day, exactly 15 years after Marzi’s release from imprisonment in Iran.' ),
	array( 'Military Support', 'Our volunteers shared meals and prayers and words of encouragement and gratitude with elite IDF units serving in Gaza, creating powerful moments of spiritual and emotional connection during challenging times.' ),
	array( 'Community Care', 'We\'ve connected with families of hostages, offering prayers and support from a unique perspective of understanding through Marzi’s personal experience of imprisonment.' ),
	array( 'Economic Support', 'Working with local olive presses, including an Arab Christian-owned business in Latrun, we\'ve created sustainable economic partnerships that cross cultural boundaries.' ),
	array( 'Spiritual Legacy', 'Through harvesting ancient olive trees near a 3,500-year-old Canaanite gate, we connect biblical history with present-day support for the people Israel.' ),
	array( 'Looking Forward', 'With hundreds of inquiries for future participation, Root &amp; Branch continues to grow as a symbol of Christian-Jewish cooperation and support for Israel. We have a big vision to provide meaningful and interactive programs year-round. Please register for information about how you can be part of the vision.' ),
);
?>
<!-- wp:group {"tagName":"section","align":"wide","className":"is-style-rb-timeline","layout":{"type":"default"}} -->
<section class="wp-block-group alignwide is-style-rb-timeline">
<?php foreach ( $root_branch_items as $root_branch_i => $root_branch_item ) : ?>
<!-- wp:group {"className":"is-style-rb-timeline-item","layout":{"type":"default"}} -->
<div class="wp-block-group is-style-rb-timeline-item"><!-- wp:heading {"level":3,"fontSize":"jumbo"} -->
<h3 class="wp-block-heading has-jumbo-font-size"><?php echo esc_html( $root_branch_item[0] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:group {"className":"rb-timeline-item__body","layout":{"type":"default"}} -->
<div class="wp-block-group rb-timeline-item__body"><!-- wp:paragraph -->
<p><?php echo wp_kses_post( $root_branch_item[1] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:image <?php echo root_branch_image_attrs( 'timeline-' . ( $root_branch_i + 1 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> -->
<figure class="wp-block-image size-large"><?php echo root_branch_img( 'timeline-' . ( $root_branch_i + 1 ), 'Root & Branch volunteers in Israel' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
<!-- /wp:image --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<?php endforeach; ?>
</section>
<!-- /wp:group -->
