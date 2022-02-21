<?php

require __DIR__ . '/vendor/autoload.php';

$includes = [
	'lib/acf.php',
	'lib/acf_eshop.php',
	'lib/cpt.php',
	'lib/site.php',
	'lib/setup.php',
	'lib/filters.php',
  'lib/helpers.php',
  'lib/admin/admin-meta-search.php',
];
foreach ($includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf('Error locating %s for inclusion', $file));
  }
  require_once $filepath;
}
unset($file, $filepath);

/**
 * Disable unwanted admin notification e-mails
 */
add_action( 'init', function() {
  // Disable notifying the admin of a new user registartion
  remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
  add_action( 'register_new_user', function( $user_id, $notify = 'user' ) {
    wp_send_new_user_notifications( $user_id, $notify );
  } );
  // Disable notifying admin of a user changing password
  remove_action( 'after_password_reset', 'wp_password_change_notification' );
} );

