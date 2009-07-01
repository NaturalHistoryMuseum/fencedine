<?php

// ------------------------ SETTINGS -------------------------------------------
$fencedine_dir  = '/var/www/fencedine.myspecies.info/files';
$fencedine_path = '/files';
$fencedine_age  = 86400;
// -----------------------------------------------------------------------------

/**
 * Fencedine is a script intended to be used as a proxy for content from a 
 * webserver
 * 
 * The script should be used for content that is particularly slow to access, 
 * and that isn't expected to change much.  Content is cached in a MySQL 
 * database making it very quick to access anything that has already been 
 * accessed.
 * 
 * The Script takes two parameters:
 * 
 * url - The URL of the content to download and return
 * 
 * age - The maximum age of the content in seconds to return.  The default for 
 *       this is 1 week
 */

// Filename
$filename = "$fencedine_dir/" . md5($_GET['url']);

// If the file exists, redirect to it.
if(file_exists($filename)){
  // Do the redirect.
  header("Location: $fencedine_path/".md5($_GET['url']));
  
  // If the file is younger than x seconds, then exit, else continue to
  // recreate the file
  if(filectime($filename) > time()-$fencedine_age){
    exit;
  }
}

exec('nohup wget "' . $_GET['url'] . '" -O ' . $filename . ' > /dev/null & echo $!');
