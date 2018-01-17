<?php

namespace Eshopiste\Helpers;

/**
 * Returns post meta min and max values
 */
function get_post_meta_value_min_max($post_type, $meta_key): array
{
  global $wpdb;
  return $wpdb->get_results( "
  	SELECT MIN(" . $wpdb->postmeta . ".meta_value) as min, MAX(" . $wpdb->postmeta . ".meta_value) as max
  	FROM " . $wpdb->posts . "
  	LEFT JOIN " . $wpdb->postmeta . "
  	ON
      " . $wpdb->postmeta . ".meta_key = '" . $meta_key . "'
      AND " . $wpdb->posts . ".ID = " . $wpdb->postmeta . ".post_id
      AND " . $wpdb->postmeta . ".meta_value > '' -- is not the empty string and is not null
  	WHERE
      " . $wpdb->posts . ".post_type = '" . $post_type . "'
      AND " . $wpdb->posts . ".post_status = 'publish'
    ",
    ARRAY_A
  )[0];
}
