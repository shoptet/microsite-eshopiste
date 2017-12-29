<?php

namespace Eshopiste\Setup;

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
