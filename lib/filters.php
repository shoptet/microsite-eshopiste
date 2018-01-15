<?php

namespace Eshopiste\Filters;

/**
 * Hide redundant categories metabox in eshop edit page
 */
add_filter('add_meta_boxes', function() {
  remove_meta_box( 'tagsdiv-eshop_category', 'eshop', 'side' );
	remove_meta_box( 'eshop_categorydiv', 'eshop', 'side' ); // if taxonomy is hierarchical
});

/**
 * Remove eshop list views for subscriber
 */
add_filter( 'views_edit-eshop', function ( $views ){
	global $current_user;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( user_can( $current_user, 'subscriber' ) ) {
    return [];
  }
  return $views;
});
