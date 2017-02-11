<?php

// This is experimental, trying out running the CDN push from the command line via `wp cli eval-file`.
namespace W3TC;

if (!is_plugin_active( 'w3-total-cache/w3-total-cache.php')) {
  echo('Aborting, plugin not ACTIVE.');
  exit(1);
}

// load class
echo('> loading...');
$w3_plugin_cdn = Dispatcher::component('Cdn_Core_Admin');

// set params and allocate output vars
echo('> allocating...');
$limit = 10;
$offset = 0;
$count = null;
$total = null;
$results = array();

echo('> exporting...');
$w3_plugin_cdn->export_library($limit, $offset, $count, $total, $results, time() + 5 );

// output
echo('> outputting...');
echo($count);
echo($total);

echo('> done.');
echo();
echo();
echo(json_encode($results));
?>