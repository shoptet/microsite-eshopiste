<?php

namespace Eshopiste\Setup;

use Urlbox\Screenshots\Urlbox;
use function Eshopiste\Helpers\handle_contact_form_submit;

\Shoptet\ShoptetExternal::init();
\Shoptet\ShoptetUserRoles::init();
\Shoptet\ShoptetStats::init();
\Shoptet\ShoptetSecurity::init();
\Shoptet\ShoptetPostCount::init();

/**
 * Register template menus
 */
add_action( 'init', function () {
	register_nav_menus([
    'header-menu' => 'Menu v hlavičce',
    'footer-menu' => 'Menu v patičce',
  ]);
});

/**
 * Remove prev and next link in e-shop detail head
 */
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );

/**
 * Remove update nag in admin
 */
add_action( 'admin_head', function() {
	remove_action( 'admin_notices', 'update_nag', 3 );
}, 1 );

/**
 * Redirect subscriber from admin dashboard to eshop list
 */
add_action('admin_init', function() {
	global $current_user, $pagenow;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if( 'index.php' === $pagenow && user_can( $current_user, 'subscriber' ) ){
    wp_redirect( admin_url( 'edit.php?post_type=eshop' ), 301 );
    exit;
  }
});

/**
 * Show only own eshop post for subscriber
 */
add_action( 'pre_get_posts', function ( $wp_query ){
  global $current_user, $pagenow;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
	// Not the correct screen, bail out
	if( !is_admin() || 'edit.php' !== $pagenow )
    return;
	// Not the correct post type, bail out
  if( 'eshop' !== $wp_query->query['post_type'] )
    return;
  if ( user_can( $current_user, 'subscriber' ) ) {
    $wp_query->set( 'author', $current_user->ID );
  }
});

/**
 * Disable admin bar for subscriber
 */
add_action('after_setup_theme', function () {
	global $current_user;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
	if ( user_can( $current_user, 'subscriber' ) ) {
		show_admin_bar(false);
	}
});

/**
 * Remove admin dashboard for subscriber
 */
add_action( 'admin_menu', function (){
	global $current_user;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( user_can( $current_user, 'subscriber' ) ) {
		remove_menu_page( 'index.php' );
  }
});

/**
 * Generate e-shop thumbnail based on its url
 */
add_action( 'save_post', function ( $post_id ){
	// Not the correct post type, bail out
	if ( 'eshop' !== get_post_type( $post_id ) ) {
		return;
	}
	// Empty url, delete post meta
	$eshop_url = get_field( 'eshop_url', $post_id );
	if ( ! isset( $eshop_url ) || trim( $eshop_url ) === '' ) {
		delete_post_meta( $post_id, 'screen_thumbnail' );
		return;
	}

	$options_default = [
		'url' => $eshop_url,
		'format' => 'jpg',
		'hide_cookie_banners' => true,
	];

	if ( $hide_selector = get_field( 'urlbox_hide_selector', $post_id ) ) {
		$options_default['hide_selector'] = $hide_selector;
		$options_default['css'] = $hide_selector . '{visibility: hidden !important}';
	}

	$urlbox = Urlbox::fromCredentials( URLBOX_API_KEY, URLBOX_API_SECRET );
	$thumbnail_sizes = [
		'large' => [
			'width' => '1280',
			'height' => '728',
			'thumb_width' => '470',
		],
		'medium' => [
			'width' => '768',
			'height' => '1024',
			'thumb_width' => '182',
		],
		'small' => [
			'width' => '375',
			'height' => '667',
			'thumb_width' => '91',
		],
	];

	foreach ($thumbnail_sizes as $size => $options) {
		$options = array_merge( $options_default, $options );
		$thumbnail_sizes[$size]['url'] = $urlbox->generateUrl( $options );
		$options['thumb_width'] *= 2;
		$thumbnail_sizes[$size]['retina_width'] = $options['thumb_width'];
		$thumbnail_sizes[$size]['retina_height'] = round( $options['height'] * $options['thumb_width'] / $options['width'] );
		$thumbnail_sizes[$size]['retina_url'] = $urlbox->generateUrl( $options );
	}

	update_post_meta( $post_id, 'screen_thumbnail', $thumbnail_sizes );
});

/**
 * Handle ordering and filtering of e-shop archive
 */
add_action('pre_get_posts', function ( $wp_query ){
	// bail early if is in admin, if not main query (allows custom code / plugins to continue working) or if not e-shop
	if ( is_admin() || !$wp_query->is_main_query() || $wp_query->get('post_type') !== 'eshop' ) return;

	$meta_query = $wp_query->get('meta_query');

	if ( $meta_query == '' ) {
		$meta_query = [];
	}

	$wp_query->set('posts_per_page', 12);

	/**
	 * Handle searching
	 */

	if( isset($_GET[ 'q' ]) ) {
		$wp_query->set('s', $_GET[ 'q' ]);
	}

	/**
	 * Handle ordering queries
	 */

	if( isset($_GET[ 'orderby' ]) ) {
		$query = explode( "_", $_GET[ 'orderby' ] );

		// skip default ordering by post_date DESC
		// e.g. '?orderby=date_asc'
		if ( $query != ['date', 'desc'] ) {
			$wp_query->set('orderby', 'meta_value_num');
			$wp_query->set('meta_key', $query[0]);
			$wp_query->set('order', $query[1]);
		}
	}

	/**
	 * Handle filtering queries
	 */

	 // Get array meta query
	 // e.g. '?query[]=0&query[]=1...'
	$getArrayMetaQuery = function ($query) {
		$result = [];
		if( isset($_GET[ $query ]) && is_array($_GET[ $query ]) ) {
			$result[] = [
				'key' => $query,
				'value' => $_GET[ $query ],
				'compare'	=> 'IN',
			];
		}
		return $result;
	};

	// Get range meta query
	// e.g. '?query_min=1000&query_max=200'
	$getRangeMetaQuery = function ($query) {
		$result = [];
		$is_min = isset($_GET[ $query . '_min' ]) && is_numeric($_GET[ $query . '_min' ]);
		$is_max = isset($_GET[ $query . '_max' ]) && is_numeric($_GET[ $query . '_max' ]);

		if ($is_min && $is_max) {
			$value = [ $_GET[ $query . '_min' ], $_GET[ $query . '_max' ] ];
			$compare = 'BETWEEN';
		} else if ($is_min) {
			$value = $_GET[ $query . '_min' ];
			$compare = '>=';
		} else if ($is_max) {
			$value = $_GET[ $query . '_max' ];
			$compare = '<=';
		} else {
			return $result;
		}

		$result[] = [
			'key' => $query,
			'value' => $value,
			'compare'	=> $compare,
			'type' => 'NUMERIC',
		];

		return $result;
	};

	$meta_query[] = $getArrayMetaQuery('category');
	$meta_query[] = $getArrayMetaQuery('type');
	$meta_query[] = $getArrayMetaQuery('age');

	$meta_query[] = $getRangeMetaQuery('price');
	$meta_query[] = $getRangeMetaQuery('turnover');
	$meta_query[] = $getRangeMetaQuery('traffic');

	$wp_query->set('meta_query', $meta_query);
});

/**
 * Join posts and postmeta tables
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_join
 */
add_filter('posts_join', function ( $join ) {
  global $wpdb;

  if ( !is_admin() && is_archive() ) {
    $join .=' LEFT JOIN '.$wpdb->postmeta. ' AS mt0 ON '. $wpdb->posts . '.ID = mt0.post_id ';
  }

  return $join;
});

/**
 * Modify the search query with posts_where
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_where
 */
add_filter( 'posts_where', function ( $where ) {
  global $wpdb;

  if ( !is_admin() && is_archive() ) {
    $where = preg_replace(
      "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
      "(".$wpdb->posts.".post_title LIKE $1)
      OR (
				(mt0.meta_key = 'description' OR mt0.meta_key = 'reason')
        AND
        (mt0.meta_value LIKE $1)
      )", $where );
  }

  return $where;
});


/**
 * Prevent duplicates
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_distinct
 */
add_filter( 'posts_distinct', function ( $where ) {
  global $wpdb;

  if ( !is_admin() && is_archive() ) {
    return "DISTINCT";
  }

  return $where;
});

/**
 * Handle ajax e-shop bid request
 */
add_action( 'wp_ajax_eshop_contact',  __NAMESPACE__ . '\\handle_eshop_contact_ajax');
add_action( 'wp_ajax_nopriv_eshop_contact', __NAMESPACE__ . '\\handle_eshop_contact_ajax');
function handle_eshop_contact_ajax () {
	handle_contact_form_submit();
  wp_die();
}

/**
 * Hide WP logo on login page
 */
add_action( 'login_enqueue_scripts', function () { ?>
  <style type="text/css">
		#login h1:first-child {display: none}
  </style>
<?php });

/**
 * Send e-mail when new e-shop is pending for review
 */
add_action( 'transition_post_status',  function ($new_status, $old_status, $post) {

	if ( get_post_type($post) !== 'eshop' || $old_status !== 'draft' || $new_status !== 'pending' ) {
		return;
  }

	$options = get_fields('options');

	if ( !isset($options['pending_email_recipients']) || !is_array($options['pending_email_recipients']) ) {
		return;
	}

	$email_recipients = $options['pending_email_recipients'];
	$eshop_id = $post->ID;

	$email_recipients_emails = [];
	foreach ($email_recipients as $user) {
		if ( !isset($user['user_email']) ) continue;
		$email_recipients_emails[] = $user['user_email'];
	}

	$eshop_title = $post->post_title;
	$email_from = $options['email_from'];
	$email_subject = $options['pending_email_subject'];
	$email_body = $options['pending_email_body'];

	$to_replace = [
		'%eshop_name%' => $eshop_title,
  ];
  $email_body = strtr($email_body, $to_replace);

	wp_mail(
		$email_recipients_emails,
		$email_subject,
		$email_body,
		[
			'From: ' . $email_from,
			'Content-Type: text/html; charset=UTF-8',
		]
	);
}, 10, 3);

/**
 * Add class for small and medium acf input field
 */
add_action('admin_head', function() {
	echo '
		<style>
	    .acf-field-small .acf-input {
				max-width: 250px !important;
	    }

			.acf-field-medium .acf-input {
				max-width: 450px !important;
	    }
  	</style>
	';
});

/**
 * Set cron for expirated e-shops
 */
if ( ! wp_next_scheduled( 'hide_expirated_eshops' ) ) {
  wp_schedule_event( time(), 'daily', 'hide_expirated_eshops' );
}
add_action( 'hide_expirated_eshops', function() {
	$options = get_fields('options');
	if ( !isset($options['eshop_expiration_time']) ) return;

	$expiration_time = $options['eshop_expiration_time'];

	$expirated_eshops_query = new \WP_Query([
	  'post_type' => 'eshop',
	  'posts_per_page' => -1,
		'date_query' => [
      'before' => date('Y-m-d', strtotime('-' . $expiration_time . ' days')),
    ],
	]);
	$expirated_eshops = $expirated_eshops_query->posts;

	foreach($expirated_eshops as $eshop) {
		\Shoptet\ShoptetLogger::capture_exception(new \Exception( "E-shop (ID: $eshop->ID) expired" ));
		wp_update_post([ 'ID' => $eshop->ID, 'post_status' => 'draft' ]);
	}

	// update count of posts in all categories
	$all_terms = get_terms([
		'taxonomy' => 'eshop_category',
    'fields' => 'ids',
    'hide_empty' => false,
	]);
	wp_update_term_count_now( $all_terms, 'eshop_category' );
});
//do_action( 'hide_expirated_eshops' );
