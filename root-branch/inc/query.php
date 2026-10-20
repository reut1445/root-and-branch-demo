<?php
/**
 * Helpers for query-loop patterns.
 *
 * @package RootBranch
 */

defined( 'ABSPATH' ) || exit;

/**
 * Query block attributes for posts in a category, by slug.
 *
 * Category IDs differ per site, so patterns resolve them when rendered.
 *
 * @param string $category_slug Category slug ("articles", "videos", "events").
 * @param int    $per_page      Posts per page.
 * @param int    $query_id      Unique query ID within the page.
 * @return string JSON attributes for <!-- wp:query -->.
 */
function root_branch_query_attrs( $category_slug, $per_page, $query_id ) {
	$term  = get_term_by( 'slug', $category_slug, 'category' );
	$query = array(
		'perPage'  => $per_page,
		'pages'    => 0,
		'offset'   => 0,
		'postType' => 'post',
		'order'    => 'desc',
		'orderBy'  => 'date',
		'author'   => '',
		'search'   => '',
		'exclude'  => array(),
		'sticky'   => '',
		'inherit'  => false,
	);
	if ( $term instanceof WP_Term ) {
		$query['taxQuery'] = array( 'category' => array( (int) $term->term_id ) );
	}
	return wp_json_encode(
		array(
			'queryId'   => $query_id,
			'query'     => $query,
			'tagName'   => 'div',
			'align'     => 'wide',
			'className' => 'rb-cards',
		)
	);
}

/**
 * Card markup used inside post templates.
 *
 * @param string $label     Small italic label above the title.
 * @param bool   $read_more Whether to show the button.
 * @param string $button    Button text.
 */
function root_branch_card_markup( $label, $read_more = true, $button = 'READ MORE →' ) {
	?>
<!-- wp:group {"className":"is-style-rb-card","layout":{"type":"default"}} -->
<div class="wp-block-group is-style-rb-card"><!-- wp:post-featured-image {"isLink":true,"sizeSlug":"medium_large"} /-->

<!-- wp:paragraph {"className":"rb-card__label"} -->
<p class="rb-card__label"><?php echo esc_html( $label ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:post-title {"level":3,"isLink":true} /-->
	<?php if ( $read_more ) : ?>

<!-- wp:read-more <?php echo wp_json_encode( array( 'content' => $button, 'className' => 'is-style-rb-button' ) ); ?> /-->
	<?php endif; ?>
</div>
<!-- /wp:group -->
	<?php
}
