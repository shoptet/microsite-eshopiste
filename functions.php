<?php

require __DIR__ . '/vendor/autoload.php';

$includes = [
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
