<?php

namespace Eshopiste\Setup;

use Urlbox\Screenshots\Urlbox;


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
    wp_redirect( admin_url( '/edit.php?post_type=eshop', 'http' ), 301 );
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
 * Remove menu items for subscriber
 */
add_action( 'admin_menu', function (){
	global $current_user;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( user_can( $current_user, 'subscriber' ) ) {
		remove_menu_page( 'index.php' );
	  remove_menu_page( 'tools.php' );
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

	$urlbox = Urlbox::fromCredentials( URLBOX_API_KEY, URLBOX_API_SECRET );
	$thumbnail_sizes = [
		'large' => [
			'width' => '1280',
			'height' => '800',
		],
		'medium' => [
			'width' => '1024',
			'height' => '768',
		],
		'small' => [
			'width' => '375',
			'height' => '667',
		],
	];

	foreach ($thumbnail_sizes as $size => $options) {
		$options['url'] = $eshop_url;
		$thumbnail_sizes[$size]['url'] = $urlbox->generateUrl( $options );
	}

	update_post_meta( $post_id, 'screen_thumbnail', $thumbnail_sizes );
});

/**
 * Handle sorting and filtering of e-shop archive
 */
add_action('pre_get_posts', function ( $wp_query ){
	// bail early if is in admin or if not main query (allows custom code / plugins to continue working)
	if ( is_admin() || !$wp_query->is_main_query() ) return;

	$meta_query = $wp_query->get('meta_query');

	if( isset($_GET[ 'category' ]) ) {
		$meta_query[] = [
      'key'		=> 'category',
      'value'		=> $_GET[ 'category' ],
      'compare'	=> 'IN',
    ];
	}

	if( isset($_GET[ 'type' ]) ) {
  	$meta_query[] = [
      'key'		=> 'type',
      'value'		=> $_GET[ 'type' ],
      'compare'	=> 'IN',
    ];
	}

	$wp_query->set('meta_query', $meta_query);
});
