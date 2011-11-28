<?php

// ------------------------ SETTINGS -------------------------------------------
$fencedine_dir  = '/var/www/fencedine.myspecies.info/files'; // Directory path
$fencedine_path = '/files'; // URL path
$fencedine_log  = 'urls'; // Log file
$fencedine_age  = 86400; // Max age of cache
$fencedine_min  = 10; // Minimum size of file to cache
// -----------------------------------------------------------------------------

/**
 * Fencedine is a script intended to be used as a proxy for content from a 
 * webserver
 * 
 * The script should be used for content that is particularly slow to access, 
 * and that isn't expected to change much.
 * 
 * The Script takes two parameters:
 * 
 * url - The URL of the content to download and return
 */

// MD5 of URL
$md5_url = md5($_GET['url']);

// Filename
$filename = "$fencedine_dir/$md5_url";

// Do the redirect.
header("Location: $fencedine_path/$md5_url");
  
// If the file doesn't exist, or is too small, try and get it again.
if(!file_exists($filename) || filesize($filename)<=$fencedine_min){  
  // The file doesn't exist, we need to download it and wait for it to be
  // downloaded before redirecting.
  exec('wget --quiet "' . $_GET['url'] . '" -O ' . $filename . ' > /dev/null');
  // Just for logging purposes (the following can be uncommented), I'm saving
  // a list of URLs accessed
  file_put_contents($fencedine_log, $_GET['url']."\n", FILE_APPEND);
}
