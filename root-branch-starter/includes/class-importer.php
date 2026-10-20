<?php
/**
 * Starter content importer.
 *
 * Page content is generated from the theme's own block patterns, so what the
 * client edits afterwards is exactly the pattern markup the theme ships.
 *
 * @package RootBranchStarter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Imports media, posts and pages. Idempotent: existing items are reused.
 */
class Root_Branch_Starter_Importer {

	/**
	 * Progress messages.
	 *
	 * @var string[]
	 */
	public $log = array();

	/**
	 * Content data (see content.php).
	 *
	 * @var array
	 */
	private $data;

	/**
	 * Uploaded attachments: media file basename => attachment ID.
	 *
	 * @var array<string,int>
	 */
	private $files = array();

	/**
	 * Run the full import.
	 */
	public function run() {
		if ( 'root-branch' !== get_template() || ! function_exists( 'root_branch_media' ) ) {
			$this->log[] = 'The Root & Branch theme must be active.';
			return;
		}

		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$this->data  = require ROOT_BRANCH_STARTER_DIR . 'includes/content.php';
		$this->files = get_option( 'root_branch_starter_files', array() );

		$this->import_media();
		$this->setup_site();
		$categories = $this->categories();
		$this->import_articles( $categories['articles'] );
		$this->import_videos( $categories['videos'] );
		$this->import_events( $categories['events'] );
		$this->import_pages();

		flush_rewrite_rules( false );
		$this->log[] = 'Done.';
	}

	/*
	 * Media
	 */

	/**
	 * Upload every photograph and publish the key => ID map for the patterns.
	 */
	private function import_media() {
		$map = get_option( 'root_branch_media_map', array() );

		foreach ( $this->data['media'] as $file => $info ) {
			$id = $this->upload( $file, $info[0] );
			foreach ( $info[1] as $key ) {
				$map[ $key ] = $id;
			}
		}

		foreach ( $this->data['gallery'] as $i => $number ) {
			$map[ 'gallery-' . ( $i + 1 ) ] = $this->upload( 'gallery-' . $number, 'Root & Branch volunteers in Israel' );
		}

		foreach ( $this->data['timeline'] as $i => $number ) {
			$map[ 'timeline-' . ( $i + 1 ) ] = $this->upload( 'gallery-' . $number, 'Root & Branch volunteers in Israel' );
		}

		update_option( 'root_branch_media_map', array_filter( $map ) );
		$this->log[] = sprintf( 'Media library: %d photographs.', count( array_filter( $this->files ) ) );
	}

	/**
	 * Upload one file from /media (once).
	 *
	 * @param string $basename File name without extension.
	 * @param string $alt      Alt text.
	 * @return int Attachment ID (0 on failure).
	 */
	private function upload( $basename, $alt ) {
		if ( ! empty( $this->files[ $basename ] ) && get_post( $this->files[ $basename ] ) ) {
			return (int) $this->files[ $basename ];
		}

		$ext    = file_exists( ROOT_BRANCH_STARTER_DIR . 'media/' . $basename . '.mp4' ) ? 'mp4' : 'webp';
		$source = ROOT_BRANCH_STARTER_DIR . 'media/' . $basename . '.' . $ext;
		if ( ! file_exists( $source ) ) {
			$this->log[] = "Missing media file: {$basename}.{$ext}";
			return 0;
		}

		$tmp = wp_tempnam( $basename . '.' . $ext );
		copy( $source, $tmp );

		$id = media_handle_sideload(
			array(
				'name'     => $basename . '.' . $ext,
				'tmp_name' => $tmp,
			),
			0,
			$alt
		);

		if ( is_wp_error( $id ) ) {
			wp_delete_file( $tmp );
			$this->log[] = "Upload failed for {$basename}: " . $id->get_error_message();
			return 0;
		}

		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
		$this->files[ $basename ] = $id;
		update_option( 'root_branch_starter_files', $this->files, false );
		return (int) $id;
	}

	/**
	 * Attachment ID for a media basename.
	 *
	 * @param string $basename Basename.
	 * @return int
	 */
	private function file_id( $basename ) {
		return isset( $this->files[ $basename ] ) ? (int) $this->files[ $basename ] : $this->upload( $basename, '' );
	}

	/*
	 * Site settings
	 */

	/**
	 * Title, logo, permalinks.
	 */
	private function setup_site() {
		$map = get_option( 'root_branch_media_map', array() );

		if ( in_array( get_option( 'blogname' ), array( '', 'My WordPress Website', 'My WordPress Site' ), true ) ) {
			update_option( 'blogname', 'Root & Branch' );
		}
		if ( in_array( get_option( 'blogdescription' ), array( '', 'Just another WordPress site' ), true ) ) {
			update_option( 'blogdescription', 'Christians Harvesting Olives in Israel' );
		}
		if ( ! get_option( 'site_logo' ) && ! empty( $map['logo'] ) ) {
			update_option( 'site_logo', (int) $map['logo'] );
		}
		if ( ! get_option( 'site_icon' ) && ! empty( $map['logo'] ) ) {
			update_option( 'site_icon', (int) $map['logo'] );
		}
		if ( '' === get_option( 'permalink_structure' ) ) {
			update_option( 'permalink_structure', '/%postname%/' );
		}
		$this->log[] = 'Site title, logo and permalinks set.';
	}

	/**
	 * Create the three post categories.
	 *
	 * @return array<string,int>
	 */
	private function categories() {
		$ids = array();
		foreach ( array(
			'articles' => array( 'Articles', 'Media coverage of Root & Branch. Give each post an External link to the article.' ),
			'videos'   => array( 'Videos', 'Interviews and films about Root & Branch.' ),
			'events'   => array( 'Events', 'Upcoming Root & Branch events.' ),
		) as $slug => $info ) {
			$term = get_term_by( 'slug', $slug, 'category' );
			if ( ! $term ) {
				$term = wp_insert_term( $info[0], 'category', array( 'slug' => $slug, 'description' => $info[1] ) );
				$ids[ $slug ] = is_wp_error( $term ) ? 0 : (int) $term['term_id'];
			} else {
				$ids[ $slug ] = (int) $term->term_id;
			}
		}
		return $ids;
	}

	/*
	 * Posts
	 */

	/**
	 * Create a post once (matched by slug).
	 *
	 * @param array $args wp_insert_post() arguments.
	 * @param int   $thumbnail Featured image ID.
	 * @return int
	 */
	private function post( $args, $thumbnail = 0 ) {
		$slug     = sanitize_title( $args['post_title'] );
		$existing = get_page_by_path( $slug, OBJECT, $args['post_type'] ?? 'post' );
		if ( $existing ) {
			return (int) $existing->ID;
		}

		$args = wp_parse_args(
			$args,
			array(
				'post_type'   => 'post',
				'post_status' => 'publish',
				'post_name'   => $slug,
			)
		);
		$id   = wp_insert_post( wp_slash( $args ), true );
		if ( is_wp_error( $id ) ) {
			$this->log[] = 'Could not create "' . $args['post_title'] . '": ' . $id->get_error_message();
			return 0;
		}
		if ( $thumbnail ) {
			set_post_thumbnail( $id, $thumbnail );
		}
		return (int) $id;
	}

	/**
	 * Media coverage articles (link out to the publication).
	 *
	 * @param int $category Category ID.
	 */
	private function import_articles( $category ) {
		foreach ( $this->data['articles'] as $article ) {
			list( $title, $url, $date, $image ) = $article;
			$id = $this->post(
				array(
					'post_title'    => $title,
					'post_date'     => $date . ' 09:00:00',
					'post_category' => array( $category ),
					'post_content'  => $this->paragraph( sprintf( 'Read the full article: <a href="%1$s">%2$s</a>', esc_url( $url ), esc_html( wp_parse_url( $url, PHP_URL_HOST ) ) ) ),
					'meta_input'    => array( 'rb_external_url' => esc_url_raw( $url ) ),
				),
				$this->file_id( $image )
			);
		}
		$this->log[] = sprintf( 'Articles: %d.', count( $this->data['articles'] ) );
	}

	/**
	 * Videos: hosted files play on the post page; podcasts link out.
	 *
	 * @param int $category Category ID.
	 */
	private function import_videos( $category ) {
		foreach ( $this->data['videos'] as $video ) {
			list( $title, $url, $date, $image, , $external ) = $video;
			$poster = wp_get_attachment_image_url( $this->file_id( $image ), 'large' );

			if ( $external ) {
				$content = $this->paragraph( sprintf( 'Listen to the full episode: <a href="%1$s">%2$s</a>', esc_url( $url ), esc_html( wp_parse_url( $url, PHP_URL_HOST ) ) ) );
				$meta    = array( 'rb_external_url' => esc_url_raw( $url ) );
			} else {
				$content = sprintf(
					"<!-- wp:video -->\n<figure class=\"wp-block-video\"><video controls preload=\"none\" poster=\"%1\$s\" src=\"%2\$s\" playsinline></video></figure>\n<!-- /wp:video -->",
					esc_url( (string) $poster ),
					esc_url( $url )
				);
				$meta    = array();
			}

			$this->post(
				array(
					'post_title'    => $title,
					'post_date'     => $date . ' 09:00:00',
					'post_category' => array( $category ),
					'post_content'  => $content,
					'meta_input'    => $meta,
				),
				$this->file_id( $image )
			);
		}
		$this->log[] = sprintf( 'Videos: %d.', count( $this->data['videos'] ) );
	}

	/**
	 * The Atlanta Unity Conference.
	 *
	 * @param int $category Category ID.
	 */
	private function import_events( $category ) {
		$register = 'https://links.rootandbranchisrael.com/widget/form/zYs85trJa9IZuCzAqTV5?notrack=true';
		$items    = array(
			'Learn about Root &amp; Branch\'s hands-on, meaningful way for Christians to make personal connections to Israel and the Israeli people',
			'Discover how Root &amp; Branch provides comfort and vision of peace with tangible benefits to Israelis in the Gaza border area',
			'Meet the founders Marziyeh Amirizadeh (Iranian American who faced death sentence for converting to Christianity) and Jonathan Feldstein',
			'Experience two main sessions over two days, including Shabbat worship and fellowship time',
			'Network with other supporters building bridges between Jews and Christians',
			'Saturday\'s "Atlanta Voices for Israel" panel discussion',
		);
		$details  = array( 'Friday, March 6, 2026 · 6 - 10pm EDT', 'Location: To be announced', 'Event lasts 4 hours', 'Refund Policy: Refunds up to 7 days before event' );

		$content = implode(
			"\n\n",
			array(
				$this->paragraph( '<strong>Root &amp; Branch Unity Conference Sponsorships - Atlanta, GA</strong><br>Friday, March 6, 2026 · 6 - 10pm EDT' ),
				$this->paragraph( '"Building Bridges &amp; Planting Roots in Israel &amp; Among the Jewish People with Unconditional Love" — By Marziyeh Amirizadeh &amp; Jonathan Feldstein' ),
				$this->button( 'REGISTER & RESERVE YOUR SPONSORSHIP', $register, true ),
				$this->paragraph( 'Join this special conference bringing Jews and Christians together to share and widen the powerful impact of Root &amp; Branch:' ),
				$this->olive_list( $items ),
				$this->heading( 'Sponsorship Opportunities Available', 3 ),
				$this->paragraph( 'Sponsorships are vital as an endorsement of the program, provide valuable seed funding for Root &amp; Branch, and help build bridges of unity between Jews and Christians. Multiple sponsorship levels available from $250 to $3600, each including VIP tickets, branded items, and special recognition.' ),
				$this->heading( 'Event Details', 3 ),
				$this->olive_list( $details ),
				$this->paragraph( 'For additional information, to join as a sponsor, register or to participate please contact <a href="mailto:rootandbranchisrael@gmail.com">RootandBranchIsrael@gmail.com</a>.' ),
			)
		);

		$this->post(
			array(
				'post_title'    => 'Root & Branch Unity Conference - Atlanta, GA | March 6-7, 2026',
				'post_name'     => 'atlanta-conference',
				'post_date'     => '2026-01-15 09:00:00',
				'post_excerpt'  => 'Building Bridges & Planting Roots in Israel & Among the Jewish People with Unconditional Love. Join Marziyeh Amirizadeh & Jonathan Feldstein for this special conference bringing Jews and Christians together.',
				'post_category' => array( $category ),
				'post_content'  => $content,
			),
			$this->file_id( 'atlanta-event' )
		);
		$this->log[] = 'Events: 1.';
	}

	/*
	 * Pages
	 */

	/**
	 * Create the six pages and set the front page.
	 */
	private function import_pages() {
		$pages = array(
			'home'    => array( 'Home', $this->home() ),
			'about'   => array( 'About', $this->about() ),
			'gallery' => array( 'Gallery', $this->gallery() ),
			'media'   => array( 'Media', $this->media() ),
			'tour'    => array( 'Tour', $this->tour() ),
			'events'  => array( 'Events', $this->events() ),
			'cancellation-policy' => array( 'Cancellation Policy', $this->cancellation(), 'page-with-title' ),
		);

		$ids = array();
		foreach ( $pages as $slug => $page ) {
			$existing = get_page_by_path( $slug );
			if ( $existing ) {
				$ids[ $slug ] = $existing->ID;
				$this->log[]  = "Page exists, left untouched: /{$slug}/";
				continue;
			}
			$ids[ $slug ] = wp_insert_post(
				wp_slash(
					array(
						'post_type'    => 'page',
						'post_status'  => 'publish',
						'post_title'   => $page[0],
						'post_name'    => $slug,
						'post_content' => $page[1],
						'menu_order'   => count( $ids ),
						'page_template' => isset( $page[2] ) ? $page[2] : '',
					)
				)
			);
			$this->log[]  = "Page created: /{$slug}/";
		}

		if ( ! empty( $ids['home'] ) && ! is_wp_error( $ids['home'] ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', (int) $ids['home'] );
		}
	}

	/**
	 * Home.
	 *
	 * @return string
	 */
	private function home() {
		return $this->join(
			array(
				$this->pattern( 'hero' ),
				$this->pattern( 'intro' ),
				$this->pattern( 'value-rows' ),
				$this->pattern( 'mission' ),
				$this->pattern( 'in-action' ),
				$this->pattern( 'media-coverage' ),
				$this->pattern( 'statement' ),
				$this->pattern( 'videos' ),
				$this->pattern( 'register' ),
			)
		);
	}

	/**
	 * About.
	 *
	 * @return string
	 */
	private function about() {
		return $this->join(
			array(
				$this->page_hero( 'About Root &amp; Branch' ),
				$this->pattern( 'founders-panel' ),
				$this->pattern( 'founder-profiles' ),
				$this->pattern( 'history' ),
				$this->pattern( 'timeline' ),
			)
		);
	}

	/**
	 * Gallery: every photograph in a masonry grid.
	 *
	 * @return string
	 */
	private function gallery() {
		$images = '';
		foreach ( $this->data['gallery'] as $i => $number ) {
			$key     = 'gallery-' . ( $i + 1 );
			$images .= '<!-- wp:image ' . root_branch_image_attrs( $key, array( 'linkDestination' => 'none' ) ) . " -->\n<figure class=\"wp-block-image size-large\">" . root_branch_img( $key, 'Root & Branch volunteers in Israel' ) . "</figure>\n<!-- /wp:image -->\n";
		}

		$section = "<!-- wp:group {\"tagName\":\"section\",\"align\":\"full\",\"className\":\"rb-gallery-page\",\"layout\":{\"type\":\"constrained\"}} -->\n<section class=\"wp-block-group alignfull rb-gallery-page\">"
			. $this->heading( 'Root &amp; Branch <span class="rb-accent">in Action</span>', 2, 'center', 'is-style-rb-ornament' )
			. "\n\n<!-- wp:gallery {\"linkTo\":\"none\",\"sizeSlug\":\"large\",\"align\":\"wide\",\"className\":\"is-style-rb-masonry\"} -->\n<figure class=\"wp-block-gallery alignwide has-nested-images columns-default is-cropped is-style-rb-masonry\">"
			. $images
			. "</figure>\n<!-- /wp:gallery --></section>\n<!-- /wp:group -->";

		return $this->join( array( $this->page_hero( 'Gallery' ), $section, $this->pattern( 'register' ) ) );
	}

	/**
	 * Media: all articles and videos.
	 *
	 * @return string
	 */
	private function media() {
		$section = function ( $title, $slug, $label, $read_more, $query_id ) {
			ob_start();
			?>
<!-- wp:group {"tagName":"section","align":"full","className":"rb-media","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull rb-media"><?php echo $this->heading( $title, 2, 'center' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

<!-- wp:query <?php echo root_branch_query_attrs( $slug, 48, $query_id ); // phpcs:ignore WordPress.Security.EscapeOutput ?> -->
<div class="wp-block-query alignwide rb-cards"><!-- wp:post-template {"className":"rb-card-grid","layout":{"type":"default"}} -->
<?php root_branch_card_markup( $label, true, $read_more ); ?>
<!-- /wp:post-template --></div>
<!-- /wp:query --></section>
<!-- /wp:group -->
			<?php
			return ob_get_clean();
		};

		return $this->join(
			array(
				$this->page_hero( 'Media Coverage' ),
				$section( 'Articles', 'articles', 'ARTICLE', 'READ MORE →', 21 ),
				$section( 'Videos', 'videos', 'VIDEO', 'WATCH VIDEO →', 22 ),
				$this->pattern( 'register' ),
			)
		);
	}

	/**
	 * Events.
	 *
	 * @return string
	 */
	private function events() {
		ob_start();
		?>
<!-- wp:group {"tagName":"section","align":"full","className":"rb-media","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull rb-media"><?php echo $this->heading( 'Events', 2, 'center', 'is-style-rb-ornament' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

<!-- wp:query <?php echo root_branch_query_attrs( 'events', 24, 31 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> -->
<div class="wp-block-query alignwide rb-cards"><!-- wp:post-template {"className":"rb-card-grid","layout":{"type":"default"}} -->
<?php root_branch_card_markup( 'CONFERENCE', true, 'EVENT DETAILS →' ); ?>
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"align":"center","className":"rb-lead","fontSize":"large"} -->
<p class="has-text-align-center rb-lead has-large-font-size">New events will be announced soon.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></section>
<!-- /wp:group -->
		<?php
		$list = ob_get_clean();

		return $this->join( array( $this->page_hero( 'Upcoming Events' ), $list, $this->pattern( 'register' ) ) );
	}

	/**
	 * Cancellation policy (linked from the Tour page).
	 *
	 * @return string
	 */
	private function cancellation() {
		return $this->section(
			'rb-policy',
			$this->heading( 'Cancellation Policy in Booking Land Package', 2, '', '', 'jumbo' )
			. "\n\n" . $this->olive_list(
				array(
					'Up to 25 days before arrival — no cancellation fees',
					'24–15 days before arrival — 30% of package cost',
					'14–7 days before arrival — 60% of package cost',
					'Less than 7 days before arrival — 100% of package cost',
				)
			)
			. "\n\n" . $this->heading( 'Force Majeure', 2, '', '', 'jumbo' )
			. "\n\n" . $this->paragraph( 'Cancellation fees will not be charged only in the event of "force majeure" — which are according to the closed list below: a general strike in the economy, a worldwide pandemic or an outbreak of disease in Israel as a result of which the event cannot be held, and this according to the guidelines of the authorized offices of the state, and any other exceptional phenomena which is not and could not have been anticipated ahead of time.' )
			. "\n\n" . $this->paragraph( 'Since at the time of booking, war had already been declared in Israel, its mere existence will not be considered force majeure. As of this date and in accordance with the directives of the Home Front Command (IDF), there is no obstacle to carrying out the booking. Only in case of a substantial and significant change in the instructions of the Home Front Command in relation to the level of defense, which will prohibit the event from taking place on the set date, no cancellation fees will be charged. In any other case, cancellation fees will be charged as mentioned above.' )
			. "\n\n" . $this->paragraph( 'In case of cancellation of a reservation that was paid with a credit card, 3% cancellation fees will apply.' )
			. "\n\n" . $this->paragraph( 'Questions about your booking or cancellation? Contact us at <a href="mailto:rootandbranchisrael@gmail.com">rootandbranchisrael@gmail.com</a>.' ),
			'1100px'
		);
	}

	/**
	 * Tour (October 2026 trip), built from the design system's sections.
	 *
	 * @return string
	 */
	private function tour() {
		$forms = array(
			array( '2 people in a double room', '$5,680', 'Total for two · $2,840 per person', 'https://links.rootandbranchisrael.com/widget/form/ItOXAd7sqrBkex6WDp4w' ),
			array( '1 person in a single room', '$3,815', 'Private room · includes the $975 single supplement', 'https://links.rootandbranchisrael.com/widget/form/ozx0J9a8M5QDFvllXI7b' ),
			array( '1 person sharing a double room', '$2,840', 'Per person, sharing with a roommate', 'https://links.rootandbranchisrael.com/widget/form/rhC87P8T9Qn9iubYt8Ei' ),
		);
		$journey = array(
			'Harvesting hundreds of years old olive trees situated in national parks and public spaces throughout southern Israel',
			'Be part of turning the olives into oil which will be sold to provide funds to bless Israel\'s most vulnerable communities and other projects and needs as a result of the war with Hamas, Hezbollah, and the Islamic Republic of Iran',
			'Interact with orphans and at-risk youth and the directors of programs that provide resilience training and other critical services',
			'Meet with local and national civic and religious leaders',
			'Bless IDF soldiers with warmth, gifts, and food',
			'Connect with families of soldiers, bereaved families who have lost loved ones due to war and terrorism, and those who have been impacted by the war',
			'Witness sites and visit communities that are on the front line and have been impacted by the war',
			'Worship together on the site of important biblical and historical events',
			'Experience a traditional Shabbat, the Sabbath, a day of rest to refresh your body and spirit',
			'Tour and experience the Galilee and Golan across millennia, and the overlaying of Biblical and modern history',
		);
		$itinerary = array(
			'Sunday, October 18, 2026'    => 'Arrival and transfer to hotel, welcome dinner.',
			'Monday, October 19, 2026'    => 'Breakfast and briefing followed by the first day of harvesting, lunch during volunteering, dinner and evening group activity.',
			'Tuesday, October 20, 2026'   => 'Early breakfast, full day of harvesting, lunch during volunteering, dinner and evening group activity.',
			'Wednesday, October 21, 2026' => 'Early breakfast, full day of harvesting, lunch during volunteering, dinner with soldiers.',
			'Thursday, October 22, 2026'  => 'Early breakfast, full day of harvesting, lunch during volunteering, dinner and evening activity to prepare for Shabbat.',
			'Friday, October 23, 2026'    => 'Early breakfast and departure for full day of touring religious and historic sites in the Galilee, lunch on own, experience how olive oil is made, return to hotel to wash up and prepare for Shabbat, festive traditional Shabbat dinner together.',
			'Saturday, October 24, 2026'  => 'Early breakfast and departure for full day of touring religious and historic sites in the Galilee and Golan, lunch on own, late return to hotel.',
			'Sunday, October 25, 2026'    => 'Breakfast followed by worship together (at historical/biblical location), harvesting, lunch during volunteering, afternoon program and dinner with Israeli leader to discuss the upcoming elections.',
			'Monday, October 26, 2026'    => 'Early breakfast, full day of harvesting, lunch during volunteering, afternoon with orphans and at-risk youth, dinner and evening activity.',
			'Tuesday, October 27, 2026'   => 'Today is scheduled to be Election Day in Israel — it will be historic just to be present as voting begins. Early breakfast and check out for those departing early, and day of activity and final harvesting for those departing late, lunch during volunteering. Safe travels — Bon Voyage — Shalom and l\'hitraot, see you again soon.',
		);
		$fellowship = 'https://fellowship.com/online-tools/root-and-branch/';

		// Hero with dates and a jump to the registration options.
		$hero = str_replace(
			"<!-- /wp:heading --></div></section>",
			"<!-- /wp:heading -->\n\n" . $this->paragraph( 'October 18–27, 2026', 'center', 'has-white-color has-text-color', array( 'textColor' => 'white' ) )
				. "\n\n<!-- wp:buttons {\"className\":\"is-style-rb-notch\",\"layout\":{\"type\":\"flex\",\"justifyContent\":\"center\"}} -->\n<div class=\"wp-block-buttons is-style-rb-notch\">"
				. $this->button_markup( 'REGISTER NOW', '#register-options' )
				. "</div>\n<!-- /wp:buttons --></div></section>",
			$this->page_hero( 'Join Root &amp; Branch for 10 Remarkable Days in Israel' )
		);

		// Journey.
		$journey_section = $this->section(
			'rb-tour-journey',
			$this->heading( 'Join us for an unforgettable journey of a lifetime from October 18–27, 2026!', 2, 'center', 'is-style-rb-ornament', 'xx-large' )
			. "\n\n" . $this->olive_list( $journey )
		);

		// Cost panel.
		$cost  = $this->heading( 'The cost for the full 10-day experience is only $2,840 <span class="rb-nowrap">(per person in a double room)</span>', 2, '', '', 'xx-large', 'white' );
		$cost .= "\n\n" . $this->paragraph( 'Which includes: half board most days, lunch on volunteering days, group transfer from and to the airport in Israel, 9 nights hotel based on double occupancy, chartered bus and all the equipment needed to harvest olives, guided tours of Jerusalem, beautiful Root &amp; Branch swag, and more.', '', 'has-large-font-size', array( 'fontSize' => 'large' ) );
		$cost .= "\n\n" . $this->paragraph( 'Cost does not include late checkout or additional days/nights in a hotel, tips, airfare, travel or medical insurance, expenses of a personal nature, lunches on arrival day, touring days, Saturday night dinner, any food, or drinks throughout the entire tour other than mentioned, any other items not mentioned.' );
		$terms = array(
			'Payment Terms'                     => array(
				'A $500 deposit (refundable until 30 days before arrival) per person is required to reserve your spot upon registration.',
				'$1,350 per person is paid 60 days in advance (or August 15).',
				'The balance needs to be paid 30 days before arrival (or September 15).',
			),
			'Refund Policy'                     => array( 'The $500 deposit is fully refundable until 30 days before arrival. After that date, the deposit becomes non-refundable. For full details on refunds and cancellations at each stage of payment, please see our <a href="/cancellation-policy/">full cancellation policy</a>.' ),
			'Private Travel &amp; Medical Insurance' => array( 'All participants are strongly required to obtain their own private travel and medical insurance prior to the trip. Neither travel nor medical insurance is provided by Root &amp; Branch. Comprehensive coverage is needed for medical emergencies, trip cancellation, flight delays, lost baggage, and other related incidents. <a href="' . $fellowship . '" target="_blank" rel="noreferrer noopener">Fellowship Travel</a> offers customized insurance options tailored to your needs.' ),
		);
		$columns = '';
		foreach ( $terms as $title => $paragraphs ) {
			$inner = $this->heading( $title, 3, '', 'rb-terms__title', '', 'white' );
			foreach ( $paragraphs as $p ) {
				$inner .= "\n\n" . $this->paragraph( $p );
			}
			$columns .= "<!-- wp:group {\"className\":\"rb-terms__item\",\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group rb-terms__item\">{$inner}</div>\n<!-- /wp:group -->\n\n";
		}
		$cost .= "\n\n<!-- wp:group {\"className\":\"rb-terms\",\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group rb-terms\">{$columns}</div>\n<!-- /wp:group -->";
		$cost_panel = "<!-- wp:group {\"tagName\":\"section\",\"align\":\"full\",\"className\":\"is-style-rb-panel rb-tour-cost\",\"backgroundColor\":\"taupe\",\"textColor\":\"white\",\"layout\":{\"type\":\"constrained\",\"contentSize\":\"1500px\"}} -->\n<section class=\"wp-block-group alignfull is-style-rb-panel rb-tour-cost has-white-color has-taupe-background-color has-text-color has-background\">{$cost}</section>\n<!-- /wp:group -->";

		// Registration options.
		$cards = '';
		foreach ( $forms as $i => $form ) {
			$forest = 1 !== $i;
			$attrs  = $forest ? '"backgroundColor":"forest","textColor":"white"' : '"backgroundColor":"sand","textColor":"black"';
			$class  = $forest ? 'has-white-color has-forest-background-color has-text-color has-background' : 'has-black-color has-sand-background-color has-text-color has-background';
			$button = $forest ? 'is-style-rb-sand rb-cta' : 'rb-cta';
			$cards .= "<!-- wp:group {\"className\":\"is-style-rb-mission-card rb-option\",{$attrs},\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group is-style-rb-mission-card rb-option {$class}\">"
				. $this->heading( $form[0], 3, 'center', 'rb-mission-card__title' )
				. "\n\n" . $this->paragraph( $form[1], 'center', 'rb-price', array( 'className' => 'rb-price' ) )
				. "\n\n" . $this->paragraph( $form[2], 'center', 'rb-price-note', array( 'className' => 'rb-price-note' ) )
				. "\n\n" . $this->paragraph( '$500 deposit per person reserves your place · 10 days, October 18–27, 2026', 'center', 'rb-option__meta', array( 'className' => 'rb-option__meta' ) )
				. "\n\n<!-- wp:buttons {\"layout\":{\"type\":\"flex\",\"justifyContent\":\"center\"}} -->\n<div class=\"wp-block-buttons\">" . $this->button_markup( 'REGISTER &amp; RESERVE →', $form[3], $button, true ) . "</div>\n<!-- /wp:buttons --></div>\n<!-- /wp:group -->\n\n";
		}
		$options = "<!-- wp:group {\"tagName\":\"section\",\"anchor\":\"register-options\",\"align\":\"full\",\"className\":\"rb-tour-options\",\"layout\":{\"type\":\"constrained\"}} -->\n<section id=\"register-options\" class=\"wp-block-group alignfull rb-tour-options\">"
			. $this->heading( 'Register <span class="rb-accent">&amp; Reserve</span>', 2, 'center' )
			. "\n\n<!-- wp:group {\"align\":\"wide\",\"className\":\"rb-options\",\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group alignwide rb-options\">{$cards}</div>\n<!-- /wp:group -->\n\n"
			. $this->paragraph( '<strong>$975 Single Supplement</strong>', 'center', 'has-large-font-size', array( 'fontSize' => 'large' ) )
			. "\n\n" . $this->paragraph( 'You may book your own flights, but we strongly recommend booking with Fellowship Travel for the group flights as this ensures the best prices, smooth layovers, and hassle-free baggage handling. With Fellowship Travel, you\'ll also have access to 24/7 service in case of emergencies or cancellations. <a href="' . $fellowship . '" target="_blank" rel="noreferrer noopener">Click here to receive your flight offer.</a>', 'center', 'rb-muted', array( 'className' => 'rb-muted' ) )
			. "</section>\n<!-- /wp:group -->";

		// About + highlights.
		$about = $this->section(
			'rb-tour-about',
			$this->heading( 'About Root &amp; Branch', 2, 'center' )
			. "\n\n" . $this->paragraph( 'Root &amp; Branch is an exciting partnership between Marziyeh (Marzi) Amirizadeh and her nonprofit, NewPersia.org, and Jonathan Feldstein and his nonprofit, the Genesis 123 Foundation.', 'center', 'rb-lead has-large-font-size', array( 'className' => 'rb-lead', 'fontSize' => 'large' ) )
			. "\n\n" . $this->paragraph( 'Marziyeh Amirizadeh is an Iranian American who immigrated to the US after being arrested and sentenced to death by hanging in Iran for the "crime" of converting to Christianity. She endured months of mental and physical hardships and intense interrogation in Evin prison — one of the most brutal prisons in the world — as a result of her faith in Jesus. After becoming a Christian, Marziyeh shared her faith regularly among fellow Iranians by distributing 20,000 bibles and running a few underground Iranian churches. In 2009, she was arrested and sentenced to death by hanging for the charges of apostasy, blasphemy, promoting Christianity, and anti-government activities. Finally, under pressure from international human rights and Christian organizations, the Iranian government released Marziyeh and she emigrated to the United States. Since becoming a Christian, Israel and the Jewish people became important to her as the land and people of Jesus, and her love for Israel and the Jewish people increased following her first visit to Israel in 2023.', '', 'rb-muted', array( 'className' => 'rb-muted' ) )
			. "\n\n" . $this->paragraph( 'Jonathan Feldstein is an Orthodox Israeli American Jew. He was born, raised, and educated in the U.S. and immigrated to Israel in 2004. He is married and the father of six, and the grandfather of four (so far). In the late 1980s, while serving as Information Officer at the Israeli Consulate in Atlanta, Jonathan visited a church for the first time, and where he witnessed Christian love and support for Israel also for the first time. This is where God called him to build bridges between Jews and Christians. Since then he has spent most of his life and career embracing this calling. He established the Genesis 123 Foundation to fellowship and build meaningful relationships with Christians as a bridge between Jews and Christians and to help connect Christians to Israel in a meaningful way.', '', 'rb-muted', array( 'className' => 'rb-muted' ) ),
			'1100px'
		);

		$highlight_images = '';
		foreach ( array( 'trip-1', 'trip-2', 'gallery-17' ) as $key ) {
			$highlight_images .= '<!-- wp:image ' . root_branch_image_attrs( $key, array( 'linkDestination' => 'none' ) ) . " -->\n<figure class=\"wp-block-image size-large\">" . root_branch_img( $key, 'Root & Branch participants in Israel' ) . "</figure>\n<!-- /wp:image -->\n";
		}
		$highlights = $this->section(
			'rb-tour-highlights',
			$this->heading( 'Highlights from Last Year\'s Trip', 2, 'center' )
			. "\n\n" . $this->paragraph( 'A glimpse of moments from our 2024 Root &amp; Branch journey through Israel.', 'center', 'rb-lead has-large-font-size', array( 'className' => 'rb-lead', 'fontSize' => 'large' ) )
			. "\n\n<!-- wp:gallery {\"linkTo\":\"none\",\"sizeSlug\":\"large\",\"align\":\"wide\",\"className\":\"is-style-rb-masonry\"} -->\n<figure class=\"wp-block-gallery alignwide has-nested-images columns-default is-cropped is-style-rb-masonry\">{$highlight_images}</figure>\n<!-- /wp:gallery -->"
		);

		// Itinerary as a timeline (text only).
		$items = '';
		foreach ( $itinerary as $day => $text ) {
			$items .= "<!-- wp:group {\"className\":\"is-style-rb-timeline-item\",\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group is-style-rb-timeline-item\">"
				. $this->heading( $day, 3, '', '', 'large' )
				. "\n\n<!-- wp:group {\"className\":\"rb-timeline-item__body\",\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group rb-timeline-item__body\">" . $this->paragraph( $text ) . "</div>\n<!-- /wp:group --></div>\n<!-- /wp:group -->\n\n";
		}
		$itinerary_section = $this->section(
			'rb-tour-itinerary',
			$this->heading( 'Itinerary', 2, 'center' )
			. "\n\n" . $this->paragraph( 'The tentative Root &amp; Branch itinerary is as follows (subject to change)', 'center', 'rb-lead has-large-font-size', array( 'className' => 'rb-lead', 'fontSize' => 'large' ) )
			. "\n\n<!-- wp:group {\"align\":\"wide\",\"className\":\"is-style-rb-timeline rb-timeline--compact\",\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group alignwide is-style-rb-timeline rb-timeline--compact\">{$items}</div>\n<!-- /wp:group -->\n\n"
			. $this->paragraph( 'As a partnership between the Genesis 123 Foundation and NewPersia.org, Root &amp; Branch embodies the shared objectives to build bridges between Jews and Christians, restore relations between Israel and Iran &amp; Persians and Jews, to protect persecuted Christians in the Middle East, and to be a blessing to the people and State of Israel. Please join us.', 'center', 'rb-lead has-large-font-size', array( 'className' => 'rb-lead', 'fontSize' => 'large' ) )
			. "\n\n<!-- wp:buttons {\"layout\":{\"type\":\"flex\",\"justifyContent\":\"center\"}} -->\n<div class=\"wp-block-buttons\">" . $this->button_markup( 'REGISTER NOW →', '#register-options', 'rb-button-lg' ) . "</div>\n<!-- /wp:buttons -->"
		);

		// Practical information.
		$info = $this->section(
			'rb-tour-info',
			$this->heading( 'Important Information for Travelers', 2, 'center' )
			. "\n\n" . $this->paragraph( '<strong>Important Notice:</strong> As of January 1st, 2026, travelers to Israel must have a valid visa or ETA-IL approval before starting their journey. A tourist who does not complete the form will not be able to check in and board the plane. The cost is NIS 25 (~$7). <a href="https://trailer.web-view.net/Links/0XFAC340ED5B08C0E8EF2863FC2F23ED236CB73026C916967DDB41DF9D3290367D419D789DD6063BE74D906786E2ADC4D18C3B904714C4F6834A5988198A779749AF5201E797B9584A.htm" target="_blank" rel="noreferrer noopener">For all details on the ETA-IL form, click here &gt;&gt;</a>' )
			. "\n\n" . $this->paragraph( 'Travelers who are not eligible for visa exemption must obtain a visa prior to entering Israel.' )
			. "\n\n" . $this->paragraph( '<strong>Custom Itineraries &amp; Private Buses:</strong> Groups wishing to bring their own bus and adapt the itinerary may do so for an additional cost. Please inquire for details at <a href="mailto:rootandbranchisrael@gmail.com">rootandbranchisrael@gmail.com</a>' )
			. "\n\n" . $this->paragraph( '<strong>Travel &amp; Medical Insurance:</strong> Neither travel nor medical insurance is provided, but both are strongly recommended. We recommend contacting your insurance provider for comprehensive coverage in the event of medical emergencies, flight delays, cancellations, lost baggage, and other related incidents. Alternatively, <a href="' . $fellowship . '" target="_blank" rel="noreferrer noopener">Fellowship Travel</a> offers customized insurance options tailored to your needs.' )
			. "\n\n" . $this->paragraph( 'See full payment terms, dates and cancellation policies <a href="/cancellation-policy/">here</a>.' )
			. "\n\n" . $this->paragraph( 'For more information about customized tours, please get in touch with Root &amp; Branch at <a href="mailto:rootandbranchisrael@gmail.com">rootandbranchisrael@gmail.com</a>.' )
			. "\n\n" . $this->paragraph( '<em>Please note: Inquiries are handled in Israel. Due to time zone differences, responses may not be immediate, but we will follow up as soon as possible.</em>', '', 'rb-muted', array( 'className' => 'rb-muted' ) ),
			'1100px'
		);

		return $this->join( array( $hero, $journey_section, $cost_panel, $options, $about, $highlights, $itinerary_section, $info, $this->pattern( 'register' ) ) );
	}

	/*
	 * Markup helpers
	 */

	/**
	 * Render a theme pattern file with the current media map.
	 *
	 * @param string $slug Pattern file name in the theme's /patterns folder.
	 * @return string
	 */
	private function pattern( $slug ) {
		$file = get_theme_file_path( "patterns/{$slug}.php" );
		if ( ! file_exists( $file ) ) {
			return '';
		}
		ob_start();
		include $file;
		return trim( ob_get_clean() );
	}

	/**
	 * Theme page hero with a custom title.
	 *
	 * @param string $title Title (HTML-escaped).
	 * @return string
	 */
	private function page_hero( $title ) {
		return str_replace( '>Page Title</h1>', '>' . $title . '</h1>', $this->pattern( 'page-hero' ) );
	}

	/**
	 * Join blocks.
	 *
	 * @param string[] $blocks Block markup.
	 * @return string
	 */
	private function join( $blocks ) {
		return implode( "\n\n", array_filter( $blocks ) );
	}

	/**
	 * Constrained section group.
	 *
	 * @param string $class   Class name.
	 * @param string $inner   Inner blocks.
	 * @param string $content Content width.
	 * @return string
	 */
	private function section( $class, $inner, $content = '' ) {
		$layout = $content ? array( 'type' => 'constrained', 'contentSize' => $content ) : array( 'type' => 'constrained' );
		$attrs  = wp_json_encode( array( 'tagName' => 'section', 'align' => 'full', 'className' => $class, 'layout' => $layout ) );
		return "<!-- wp:group {$attrs} -->\n<section class=\"wp-block-group alignfull {$class}\">{$inner}</section>\n<!-- /wp:group -->";
	}

	/**
	 * Paragraph block.
	 *
	 * @param string $html    Inner HTML.
	 * @param string $align   Text alignment.
	 * @param string $classes Extra classes on the element.
	 * @param array  $attrs   Block attributes.
	 * @return string
	 */
	private function paragraph( $html, $align = '', $classes = '', $attrs = array() ) {
		if ( $align ) {
			$attrs = array( 'align' => $align ) + $attrs;
		}
		$class = trim( ( $align ? "has-text-align-{$align} " : '' ) . $classes );
		$json  = $attrs ? ' ' . wp_json_encode( $attrs ) : '';
		$class = $class ? " class=\"{$class}\"" : '';
		return "<!-- wp:paragraph{$json} -->\n<p{$class}>{$html}</p>\n<!-- /wp:paragraph -->";
	}

	/**
	 * Heading block.
	 *
	 * @param string $html      Inner HTML.
	 * @param int    $level     Level.
	 * @param string $align     Text alignment.
	 * @param string $class     Class name.
	 * @param string $font_size Font size slug.
	 * @param string $color     Text colour slug.
	 * @return string
	 */
	private function heading( $html, $level = 2, $align = '', $class = '', $font_size = '', $color = '' ) {
		$attrs = array();
		if ( $align ) {
			$attrs['textAlign'] = $align;
		}
		if ( 2 !== $level ) {
			$attrs['level'] = $level;
		}
		if ( $class ) {
			$attrs['className'] = $class;
		}
		if ( $color ) {
			$attrs['textColor'] = $color;
		}
		if ( $font_size ) {
			$attrs['fontSize'] = $font_size;
		}
		$classes = array_filter(
			array(
				'wp-block-heading',
				$align ? "has-text-align-{$align}" : '',
				$class,
				$color ? "has-{$color}-color has-text-color" : '',
				$font_size ? "has-{$font_size}-font-size" : '',
			)
		);
		$json = $attrs ? ' ' . wp_json_encode( $attrs ) : '';
		return "<!-- wp:heading{$json} -->\n<h{$level} class=\"" . implode( ' ', $classes ) . "\">{$html}</h{$level}>\n<!-- /wp:heading -->";
	}

	/**
	 * Button block (without the Buttons wrapper).
	 *
	 * @param string $text     Text.
	 * @param string $url      URL.
	 * @param string $class    Class name.
	 * @param bool   $new_tab  Open in a new tab.
	 * @return string
	 */
	private function button_markup( $text, $url, $class = '', $new_tab = false ) {
		$attrs  = $class ? ' ' . wp_json_encode( array( 'className' => $class ) ) : '';
		$target = $new_tab ? ' target="_blank" rel="noreferrer noopener"' : '';
		$wrap   = trim( 'wp-block-button ' . $class );
		return "<!-- wp:button{$attrs} -->\n<div class=\"{$wrap}\"><a class=\"wp-block-button__link wp-element-button\" href=\"" . esc_url( $url ) . "\"{$target}>{$text}</a></div>\n<!-- /wp:button -->";
	}

	/**
	 * Buttons wrapper with one button.
	 *
	 * @param string $text    Text.
	 * @param string $url     URL.
	 * @param bool   $new_tab Open in a new tab.
	 * @return string
	 */
	private function button( $text, $url, $new_tab = false ) {
		return "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\">" . $this->button_markup( esc_html( $text ), $url, '', $new_tab ) . "</div>\n<!-- /wp:buttons -->";
	}

	/**
	 * List with olive bullets.
	 *
	 * @param string[] $items Items (HTML).
	 * @param string   $align Block alignment.
	 * @return string
	 */
	private function olive_list( $items, $align = '' ) {
		$li = '';
		foreach ( $items as $item ) {
			$li .= "<!-- wp:list-item -->\n<li>{$item}</li>\n<!-- /wp:list-item -->\n\n";
		}
		$attrs = array( 'className' => 'is-style-rb-olive' );
		$class = 'wp-block-list is-style-rb-olive';
		if ( $align ) {
			$attrs = array( 'align' => $align ) + $attrs;
			$class = "wp-block-list align{$align} is-style-rb-olive";
		}
		return '<!-- wp:list ' . wp_json_encode( $attrs ) . " -->\n<ul class=\"{$class}\">{$li}</ul>\n<!-- /wp:list -->";
	}
}
