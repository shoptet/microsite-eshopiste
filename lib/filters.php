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
 * Rewrite Pagination slug rules
 */
add_filter( 'login_message', function ($message) {

  $new_message = '';

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
