<?php
/**
 * Sign-ups: storage, submission handling, admin list, CSV export, privacy.
 *
 * Submissions are stored as a private post type so they appear in the
 * dashboard (Sign-ups) and can be exported. A notification is emailed to the
 * address set in Settings → General → "Sign-up notifications".
 *
 * Spam protection without third-party services: a hidden honeypot field, a
 * minimum fill time and a per-visitor rate limit.
 *
 * @package RootBranchCore
 */

defined( 'ABSPATH' ) || exit;

const ROOT_BRANCH_SIGNUP_TYPE = 'rb_signup';

/**
 * Human-readable error messages.
 *
 * @param string $code Error code.
 * @return string
 */
function root_branch_core_signup_error_message( $code ) {
	$messages = array(
		'invalid'  => __( 'Please enter your name and a valid email address.', 'root-branch-core' ),
		'too-many' => __( 'Too many attempts. Please wait a few minutes and try again.', 'root-branch-core' ),
		'failed'   => __( 'Something went wrong. Please try again.', 'root-branch-core' ),
	);
	return isset( $messages[ $code ] ) ? $messages[ $code ] : $messages['failed'];
}

/*
 * Post type.
 */
add_action(
	'init',
	static function () {
		register_post_type(
			ROOT_BRANCH_SIGNUP_TYPE,
			array(
				'labels'          => array(
					'name'               => __( 'Sign-ups', 'root-branch-core' ),
					'singular_name'      => __( 'Sign-up', 'root-branch-core' ),
					'menu_name'          => __( 'Sign-ups', 'root-branch-core' ),
					'all_items'          => __( 'All sign-ups', 'root-branch-core' ),
					'edit_item'          => __( 'Sign-up', 'root-branch-core' ),
					'search_items'       => __( 'Search sign-ups', 'root-branch-core' ),
					'not_found'          => __( 'No sign-ups yet.', 'root-branch-core' ),
					'not_found_in_trash' => __( 'No sign-ups in the trash.', 'root-branch-core' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'show_in_rest'    => false,
				'menu_position'   => 26,
				'menu_icon'       => 'dashicons-email-alt',
				'supports'        => array( 'title' ),
				'capability_type' => 'post',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
				'rewrite'         => false,
				'query_var'       => false,
			)
		);
	}
);

/*
 * Submission handler (logged-in and anonymous).
 */
add_action( 'admin_post_nopriv_root_branch_signup', 'root_branch_core_handle_signup' );
add_action( 'admin_post_root_branch_signup', 'root_branch_core_handle_signup' );

/**
 * Validate, store and notify.
 */
function root_branch_core_handle_signup() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- anonymous public form; see file header. Logged-in users are nonce-checked below.
	$is_ajax = ! empty( $_POST['rb_ajax'] );

	if ( is_user_logged_in() && ! wp_verify_nonce( isset( $_POST['rb_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['rb_nonce'] ) ) : '', 'root_branch_signup' ) ) {
		root_branch_core_signup_respond( false, 'failed', $is_ajax );
	}

	$honeypot = isset( $_POST['rb_website'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['rb_website'] ) ) ) : '';
	$started  = isset( $_POST['rb_ts'] ) ? absint( $_POST['rb_ts'] ) : 0;

	// Bots: pretend success so they do not retry.
	if ( '' !== $honeypot || ( $started && time() - $started < 3 ) ) {
		root_branch_core_signup_respond( true, '', $is_ajax );
	}

	$ip_key   = 'rb_signup_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
	$attempts = (int) get_transient( $ip_key );
	if ( $attempts >= 5 ) {
		root_branch_core_signup_respond( false, 'too-many', $is_ajax );
	}
	set_transient( $ip_key, $attempts + 1, 10 * MINUTE_IN_SECONDS );

	$name  = isset( $_POST['rb_name'] ) ? sanitize_text_field( wp_unslash( $_POST['rb_name'] ) ) : '';
	$email = isset( $_POST['rb_email'] ) ? sanitize_email( wp_unslash( $_POST['rb_email'] ) ) : '';
	$phone = isset( $_POST['rb_phone'] ) ? preg_replace( '/[^0-9+()\-.\s]/', '', sanitize_text_field( wp_unslash( $_POST['rb_phone'] ) ) ) : '';
	$list  = isset( $_POST['rb_list'] ) ? sanitize_text_field( wp_unslash( $_POST['rb_list'] ) ) : '';
	// phpcs:enable

	if ( '' === $name || ! is_email( $email ) || mb_strlen( $name ) > 200 || mb_strlen( (string) $phone ) > 40 ) {
		root_branch_core_signup_respond( false, 'invalid', $is_ajax );
	}

	$source = wp_get_referer();
	$post_id = wp_insert_post(
		array(
			'post_type'   => ROOT_BRANCH_SIGNUP_TYPE,
			'post_status' => 'private',
			'post_title'  => $name,
			'meta_input'  => array(
				'_rb_email'  => $email,
				'_rb_phone'  => $phone,
				'_rb_list'   => $list,
				'_rb_source' => $source ? esc_url_raw( $source ) : '',
			),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		root_branch_core_signup_respond( false, 'failed', $is_ajax );
	}

	$to = get_option( 'root_branch_signup_recipient' );
	$to = is_email( $to ) ? $to : get_option( 'admin_email' );
	wp_mail(
		$to,
		/* translators: %s: person's name. */
		sprintf( __( 'New sign-up: %s', 'root-branch-core' ), $name ),
		implode(
			"\n",
			array_filter(
				array(
					__( 'Name:', 'root-branch-core' ) . ' ' . $name,
					__( 'Email:', 'root-branch-core' ) . ' ' . $email,
					$phone ? __( 'Phone:', 'root-branch-core' ) . ' ' . $phone : '',
					$list ? __( 'Form:', 'root-branch-core' ) . ' ' . $list : '',
					$source ? __( 'Page:', 'root-branch-core' ) . ' ' . $source : '',
					'',
					__( 'All sign-ups:', 'root-branch-core' ) . ' ' . admin_url( 'edit.php?post_type=' . ROOT_BRANCH_SIGNUP_TYPE ),
				)
			)
		),
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);

	root_branch_core_signup_respond( true, '', $is_ajax );
}

/**
 * JSON for the enhanced form, redirect back for the plain form. Exits.
 *
 * @param bool   $ok      Success.
 * @param string $code    Error code.
 * @param bool   $is_ajax Whether the request came from view.js.
 */
function root_branch_core_signup_respond( $ok, $code, $is_ajax ) {
	if ( $is_ajax ) {
		if ( $ok ) {
			wp_send_json_success();
		}
		wp_send_json_error( array( 'message' => root_branch_core_signup_error_message( $code ) ), 'too-many' === $code ? 429 : 400 );
	}

	$back = wp_get_referer();
	$back = $back ? $back : home_url( '/' );
	$back = remove_query_arg( 'rb-signup', strtok( $back, '#' ) );
	wp_safe_redirect( add_query_arg( 'rb-signup', $ok ? 'success' : $code, $back ) . '#sign-up', 303 );
	exit;
}

/*
 * Settings → General: notification address.
 */
add_action(
	'admin_init',
	static function () {
		register_setting(
			'general',
			'root_branch_signup_recipient',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_email',
				'default'           => '',
			)
		);
		add_settings_field(
			'root_branch_signup_recipient',
			__( 'Sign-up notifications', 'root-branch-core' ),
			static function () {
				printf(
					'<input type="email" class="regular-text ltr" id="root_branch_signup_recipient" name="root_branch_signup_recipient" value="%1$s" placeholder="%2$s"><p class="description">%3$s</p>',
					esc_attr( get_option( 'root_branch_signup_recipient' ) ),
					esc_attr( get_option( 'admin_email' ) ),
					esc_html__( 'New sign-ups are emailed here. Leave empty to use the administration email address.', 'root-branch-core' )
				);
			},
			'general',
			'default',
			array( 'label_for' => 'root_branch_signup_recipient' )
		);
	}
);

/*
 * Admin list columns.
 */
add_filter(
	'manage_' . ROOT_BRANCH_SIGNUP_TYPE . '_posts_columns',
	static function () {
		return array(
			'cb'       => '<input type="checkbox" />',
			'title'    => __( 'Name', 'root-branch-core' ),
			'rb_email' => __( 'Email', 'root-branch-core' ),
			'rb_phone' => __( 'Phone', 'root-branch-core' ),
			'rb_list'  => __( 'Form', 'root-branch-core' ),
			'date'     => __( 'Date', 'root-branch-core' ),
		);
	}
);

add_action(
	'manage_' . ROOT_BRANCH_SIGNUP_TYPE . '_posts_custom_column',
	static function ( $column, $post_id ) {
		$value = get_post_meta( $post_id, '_' . $column, true );
		if ( 'rb_email' === $column && $value ) {
			printf( '<a href="mailto:%1$s">%1$s</a>', esc_html( $value ) );
			return;
		}
		echo esc_html( $value );
	},
	10,
	2
);

// Read-only details on the edit screen.
add_action(
	'add_meta_boxes_' . ROOT_BRANCH_SIGNUP_TYPE,
	static function () {
		add_meta_box(
			'rb-signup-details',
			__( 'Details', 'root-branch-core' ),
			static function ( $post ) {
				echo '<table class="form-table" role="presentation"><tbody>';
				foreach ( array(
					'_rb_email'  => __( 'Email', 'root-branch-core' ),
					'_rb_phone'  => __( 'Phone', 'root-branch-core' ),
					'_rb_list'   => __( 'Form', 'root-branch-core' ),
					'_rb_source' => __( 'Page', 'root-branch-core' ),
				) as $key => $label ) {
					printf( '<tr><th scope="row">%s</th><td>%s</td></tr>', esc_html( $label ), esc_html( get_post_meta( $post->ID, $key, true ) ) );
				}
				echo '</tbody></table>';
			},
			null,
			'normal',
			'high'
		);
	}
);

// Search by email too.
add_filter(
	'posts_search',
	static function ( $search, $query ) {
		global $wpdb;
		if ( ! is_admin() || ! $query->is_main_query() || ROOT_BRANCH_SIGNUP_TYPE !== $query->get( 'post_type' ) || '' === $query->get( 's' ) ) {
			return $search;
		}
		$search = preg_replace( '/^\s*AND\s*/', '', $search );
		if ( '' === $search ) {
			return $search;
		}
		$like = '%' . $wpdb->esc_like( $query->get( 's' ) ) . '%';
		return ' AND (' . $search . $wpdb->prepare( " OR {$wpdb->posts}.ID IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_rb_email' AND meta_value LIKE %s))", $like );
	},
	10,
	2
);

/*
 * CSV export.
 */
add_action(
	'restrict_manage_posts',
	static function ( $post_type ) {
		if ( ROOT_BRANCH_SIGNUP_TYPE !== $post_type ) {
			return;
		}
		printf(
			'<a class="button" href="%s">%s</a>',
			esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=root_branch_signup_export' ), 'root_branch_signup_export' ) ),
			esc_html__( 'Export CSV', 'root-branch-core' )
		);
	}
);

add_action(
	'admin_post_root_branch_signup_export',
	static function () {
		if ( ! current_user_can( 'edit_others_posts' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to export sign-ups.', 'root-branch-core' ), 403 );
		}
		check_admin_referer( 'root_branch_signup_export' );

		$ids = get_posts(
			array(
				'post_type'      => ROOT_BRANCH_SIGNUP_TYPE,
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=root-branch-signups-' . gmdate( 'Y-m-d' ) . '.csv' );

		$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		fwrite( $out, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- UTF-8 BOM for Excel.
		fputcsv( $out, array( 'Name', 'Email', 'Phone', 'Form', 'Page', 'Date' ) );
		foreach ( $ids as $id ) {
			fputcsv(
				$out,
				array_map(
					'root_branch_core_csv_cell',
					array(
						get_the_title( $id ),
						get_post_meta( $id, '_rb_email', true ),
						get_post_meta( $id, '_rb_phone', true ),
						get_post_meta( $id, '_rb_list', true ),
						get_post_meta( $id, '_rb_source', true ),
						get_the_date( 'Y-m-d H:i', $id ),
					)
				)
			);
		}
		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		exit;
	}
);

/**
 * Neutralise spreadsheet formulas in exported cells.
 *
 * @param string $value Cell value.
 * @return string
 */
function root_branch_core_csv_cell( $value ) {
	$value = (string) $value;
	return preg_match( '/^[=+\-@\t\r]/', $value ) ? "'" . $value : $value;
}

/*
 * Privacy: include sign-ups in WordPress' personal data export/erase tools.
 */
add_filter(
	'wp_privacy_personal_data_exporters',
	static function ( $exporters ) {
		$exporters['root-branch-signups'] = array(
			'exporter_friendly_name' => __( 'Root & Branch sign-ups', 'root-branch-core' ),
			'callback'               => static function ( $email ) {
				$items = array();
				foreach ( root_branch_core_signups_for_email( $email ) as $id ) {
					$items[] = array(
						'group_id'    => 'root-branch-signups',
						'group_label' => __( 'Sign-ups', 'root-branch-core' ),
						'item_id'     => 'rb-signup-' . $id,
						'data'        => array(
							array( 'name' => __( 'Name', 'root-branch-core' ), 'value' => get_the_title( $id ) ),
							array( 'name' => __( 'Email', 'root-branch-core' ), 'value' => get_post_meta( $id, '_rb_email', true ) ),
							array( 'name' => __( 'Phone', 'root-branch-core' ), 'value' => get_post_meta( $id, '_rb_phone', true ) ),
							array( 'name' => __( 'Date', 'root-branch-core' ), 'value' => get_the_date( 'Y-m-d H:i', $id ) ),
						),
					);
				}
				return array( 'data' => $items, 'done' => true );
			},
		);
		return $exporters;
	}
);

add_filter(
	'wp_privacy_personal_data_erasers',
	static function ( $erasers ) {
		$erasers['root-branch-signups'] = array(
			'eraser_friendly_name' => __( 'Root & Branch sign-ups', 'root-branch-core' ),
			'callback'             => static function ( $email ) {
				$ids = root_branch_core_signups_for_email( $email );
				foreach ( $ids as $id ) {
					wp_delete_post( $id, true );
				}
				return array(
					'items_removed'  => count( $ids ) > 0,
					'items_retained' => false,
					'messages'       => array(),
					'done'           => true,
				);
			},
		);
		return $erasers;
	}
);

/**
 * Sign-up IDs for an email address.
 *
 * @param string $email Email.
 * @return int[]
 */
function root_branch_core_signups_for_email( $email ) {
	return get_posts(
		array(
			'post_type'      => ROOT_BRANCH_SIGNUP_TYPE,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => '_rb_email', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => sanitize_email( $email ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
}
