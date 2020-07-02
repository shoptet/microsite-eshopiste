<?php

require __DIR__ . '/vendor/autoload.php';

$includes = [
	'lib/acf.php',
	'lib/cpt.php',
	'lib/site.php',
	'lib/setup.php',
	'lib/filters.php',
  'lib/helpers.php',
];
foreach ($includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf('Error locating %s for inclusion', $file));
  }
  require_once $filepath;
}
unset($file, $filepath);

function get_shoptet_footer() {
    // params
    $id = 'eshopistecz';
    $temp = 'wp-content/themes/eshopiste-theme/tmp/shoptet-footer.html';

    $url = 'https://www.shoptet.cz/action/ShoptetFooter/render/';
    $cache = 24 * 60 * 60;
    $probability = 50;
    $ignoreTemp = isset($_GET['force_footer']);

    // code
    $footer = '';
    if (!$ignoreTemp && is_readable($temp)) {
        $footer = file_get_contents($temp);
        $regenerate = rand(1, $probability) === $probability;
        if (!$regenerate) {
            return $footer;
        }
        $mtine = filemtime($temp);
        if ($mtine + $cache > time()) {
            return $footer;
        }
    }

    $address = $url . '?id=' . urlencode($id);
    $new = file_get_contents($address);
    if ($new !== FALSE) {
        $newTemp = $temp . '.new';
        $length = strlen($new);
        $result = file_put_contents($newTemp, $new);
        if ($result === $length) {
            rename($newTemp, $temp);
        }
        $footer = $new;
    }

    return $footer;
}

add_filter( 'get_shoptet_footer', 'get_shoptet_footer' );

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

