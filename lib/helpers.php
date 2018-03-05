<?php

namespace Eshopiste\Helpers;

/**
 * Returns post meta min and max values
 */
function get_post_meta_value_min_max($post_type, $meta_key): array
{
  global $wpdb;
  return $wpdb->get_results("
  	SELECT
      MIN(CAST(" . $wpdb->postmeta . ".meta_value as UNSIGNED)) as min, -- convert longtext meta_value to unsigned
      MAX(CAST(" . $wpdb->postmeta . ".meta_value as UNSIGNED)) as max -- convert longtext meta_value to unsigned
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

/**
 * Returns post meta sum
 */
function get_post_meta_sum($post_type, $meta_key) {
  global $wpdb;
  return $wpdb->get_var("
  	SELECT
      SUM(CAST(" . $wpdb->postmeta . ".meta_value as UNSIGNED)) -- convert longtext meta_value to unsigned
  	FROM " . $wpdb->posts . "
  	LEFT JOIN " . $wpdb->postmeta . "
  	ON
      " . $wpdb->postmeta . ".meta_key = '" . $meta_key . "'
      AND " . $wpdb->posts . ".ID = " . $wpdb->postmeta . ".post_id
      AND " . $wpdb->postmeta . ".meta_value > '' -- is not the empty string and is not null
  	WHERE
      " . $wpdb->posts . ".post_type = '" . $post_type . "'
      AND (" . $wpdb->posts . ".post_status = 'publish' OR " . $wpdb->posts . ".post_status = 'draft')
  ");
}

/**
 * Returns link to e-shop category
 */
function get_eshop_category_link($term_id): string
{
  return get_post_type_archive_link('eshop') . '?category%5B%5D=' . $term_id;
}

/**
 * Returns posts from the same categories
 */
function get_similiar_posts($post, $number = 4) {
  $similarPosts = [];

  // Collect all post from all post categories
  foreach ($post->terms() as $term) {
  	$similarPosts = array_merge($similarPosts, $term->posts());
  }

  // Exclude own post from similar posts
  $similarPosts = array_filter($similarPosts, function ($similarPost) use ($post) {
  	return ($similarPost->id !== $post->id);
  });

  $similarPosts = array_unique($similarPosts);
  shuffle($similarPosts);
  $similarPosts = array_slice($similarPosts, 0, $number);

  return $similarPosts;
}

/**
 * Handle e-shop contact form submit
 */
function handle_contact_form_submit() {
  $name = sanitize_text_field( $_POST['name'] );
	$email = sanitize_email( $_POST['email'] );
	$message = sanitize_textarea_field( $_POST['message'] );
	$eshop_id = intval( $_POST['eshop_id'] );

	$postarr = [
		'post_type' => 'eshop_bid',
	  'post_title' => $name,
		'post_status' => 'publish',
		'meta_input' => [
			'email' => $email,
			'message' => $message,
			'eshop' => $eshop_id,
		],
	];
	wp_insert_post( $postarr );

  $eshop_title = get_the_title( $eshop_id );
  $eshop_contact_email = get_post_meta( $eshop_id, 'contact_email' );

  $options = get_fields('options');
  $email_from = $options['email_from'];
  $seller_email_body = $options['seller_email_body'];
  $seller_email_subject = $options['seller_email_subject'];
  $buyer_email_body = $options['buyer_email_body'];
  $buyer_email_subject = $options['buyer_email_subject'];

  $to_replace = [
    '%contact_name%' => $name,
    '%contact_email%' => $email,
    '%contact_message%' => $message,
    '%eshop_name%' => $eshop_title,
  ];
  $seller_email_body = strtr($seller_email_body, $to_replace);
  $buyer_email_body = strtr($buyer_email_body, $to_replace);

  // Send e-mail to seller
	wp_mail(
    $eshop_contact_email,
    $seller_email_subject,
		$seller_email_body,
		[
			'From: ' . $email_from,
			'Replay-to: ' . $email,
      'Content-Type: text/html; charset=UTF-8',
		]
	);

  // Send e-mail to buyer
  wp_mail(
		$email,
    $buyer_email_subject,
		$buyer_email_body,
		[
			'From: ' . $email_from,
      'Content-Type: text/html; charset=UTF-8',
		]
	);

}

/**
 * Get truncated string
 */
function truncate( $string, $limit, $separator = '...' ) {
  if (strlen($string) <= $limit) return $string;
  $newlimit = $limit - strlen($separator);
  $s = substr($string, 0, $newlimit + 1);
  return substr($s, 0, strrpos($s, ' ')) . $separator;
}
