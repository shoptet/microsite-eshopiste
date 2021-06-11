<?php

namespace Eshopiste\Filters;

use function Eshopiste\Helpers\get_post_meta_sum;
use function Eshopiste\Helpers\get_password_reset_url;

/**
 * Edit robots.txt file
 */
add_filter('robots_txt', function( $robots_text ) {
  // via https://moz.com/community/q/default-robots-txt-in-wordpress-should-i-change-it#reply_329849
  return $robots_text . '
Disallow: /?p=
Disallow: /wp-includes/
Disallow: /wp-login.php
Disallow: /wp-register.php
';
});

/**
 * Add query arguments to post count api
 */
add_filter( 'shoptet_post_count_query_args', function($query_args) {
	return [
		'eshopisteInvestorsCount' => [
			'post_type' => 'eshop_bid',
			'post_status' => 'publish',
		],
		'eshopisteProjectsCount' => [
			'post_type' => 'eshop',
			'post_status' => [ 'publish', 'draft' ],
		],
	];
} );

add_filter( 'shoptet_post_count_result', function ( $items ) {
  $items['eshopisteSales'] = intval( get_post_meta_sum( 'eshop', 'turnover' ) );
  return $items;
} );

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
  if ( user_can( $current_user, 'subscriber' ) ) return [];
  return $views;
});

/**
 * Rewrite Pagination slug rules
 */
add_filter('init', function () {
  global $wp_rewrite;
  $wp_rewrite->pagination_base = 'strana';
  $wp_rewrite->flush_rules();
}, 0);

/**
 * Update login header
 */
add_filter( 'login_message', function ($message) {

  $new_message = '
    <a href="' . get_home_url() . '">
      <img
        src="' . get_template_directory_uri() . '/assets/eshopiste-logo-no-claim.svg"
        style="
          display: block;
          margin: 0 auto 50px auto;
          max-width: 230px;
        "
      >
    </a>
  ';

  // Add title to login pages
  if ( ! isset( $_REQUEST['action'] ) )
    $new_message .= '<h1 style="margin-bottom:20px">Přihlášení</h1>';
  else if ( $_REQUEST['action'] === 'register' )
    $new_message .= '<h1 style="margin-bottom:20px">Registrace</h1>';
  else if ( $_REQUEST['action'] === 'lostpassword' )
    $new_message .= '<h1 style="margin-bottom:20px">Zapomenuté heslo</h1>';

  // Add messages to login pages
  if ( ! isset( $_REQUEST['action'] ) )
    $new_message .= '
      <p class="message">
        Nemáte-li vytvořený účet, nejprve se <a href="' . wp_registration_url() . '">registrujte</a>
      </p>
    ';
  else if ( $_REQUEST['action'] === 'register' )
    $new_message .= '
      <p class="message">
        Zvolte si uživatelské jméno a vložte svůj e-mail
      </p>
      <p class="message">
        Pokud již máte vytvořený účet, <a href="' . wp_login_url() . '">přihlašte se</a>
      </p>
    ';
  else
    $new_message .= $message;

  return $new_message;
});

/**
 * Update login footer
 */
add_filter( 'login_footer', function () {
  echo '
    <a href="https://www.shoptet.cz/" target="_blank">
      <img
        src="' . get_template_directory_uri() . '/assets/shoptet-logo.svg"
        style="
          display: block;
          max-width: 120px;
          margin: 50px auto 50px auto;
        "
      >
    </a>
  ';
});

/**
 * Redirect subscriber to admin e-shop list after login
 */
add_filter( 'login_redirect', function( $redirect_to, $request, $user ) {
  if ( isset( $user->roles ) && is_array( $user->roles ) ) {
    if ( in_array( 'subscriber', $user->roles ) ) {
      return admin_url( 'edit.php?post_type=eshop' );
    }
  }
  return $redirect_to;
}, 10, 3);

/**
 * Edit new user notification e-mail
 */
add_filter( 'wp_new_user_notification_email', function( $email, $user ) {
  $set_password_url = get_password_reset_url($user);

  $options = get_fields('options');

  $email_from = $options['email_from'];
	$email_subject = $options['welcome_email_subject'];
	$email_body = $options['welcome_email_body'];

	$to_replace = [
		'%username%' => $user->user_login,
		'%set_password_url%' => "<a href=\"$set_password_url\">$set_password_url</a>",
  ];
  $email_body = strtr($email_body, $to_replace);

  $email['subject'] = $email_subject;
  $email['message'] = $email_body;
  $email['headers'] = [
    'From: ' . $email_from,
    'Content-Type: text/html; charset=UTF-8',
  ];

  return $email;
}, 10, 2);

