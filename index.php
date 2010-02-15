<?php

// ------------------------ SETTINGS -------------------------------------------
$fencedine_dir  = '/var/www/fencedine.myspecies.info/files';
$fencedine_path = '/files';
$fencedine_log  = 'urls';
$fencedine_age  = 86400;
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
  
// If the file exists, redirect to it.
if(file_exists($filename)){  
  // If the file is older than x seconds, then silently recreate the file
  if(filectime($filename) < time()-$fencedine_age){
    exec('nohup wget --quiet "' . $_GET['url'] . '" -O ' . $filename . ' > /dev/null & echo $!');
  }
} else {
  // The file doesn't exist, we need to download it and wait for it to be
  // downloaded before redirecting.
  exec('wget --quiet "' . $_GET['url'] . '" -O ' . $filename . ' > /dev/null');
  // Just for logging purposes (the following can be uncommented), I'm saving
  // a list of URLs accessed
  file_put_contents($fencedine_log, $_GET['url']."\n", FILE_APPEND);
}
