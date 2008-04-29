<?php

/**
 * Fencedine is a script intended to be used as a proxy for content from a webserver
 * 
 * The script should be used for content that is particularly slow to access, and that
 * isn't expected to change much.  Content is cached in a MySQL database making it very
 * quick to access anything that has already been accessed.
 * 
 * The Script takes two parameters:
 * 
 * url - The URL of the content to download and return
 * 
 * age - The maximum age of the content in seconds to return.  The default for this is 
 *       1 week
 */

// Get the parameters and decode them
$url = urldecode($_GET['url']);
if(isset($_GET['age'])){
  $age = $_GET['age'];
}else{
  $age = 60*60*24*7;
}

// If you want to include this script in a project, comment out the following line, and 
// use the function fencedine_get_content.
echo fencedine_get_content($url, $age);

function fencedine_get_content($url, $age){
  /**
   * The following lines should be changed according to your requirements
   */
  mysql_connect('localhost','fencedine','vbjGFt3D7NqGTeJA');
  mysql_select_db('fencedine');
  
  // Select the content from the database.
  $sql = "SELECT content FROM content WHERE hash='".md5($url)."' AND age > ".(time()-$age);
  $results = mysql_query($sql);
  while($row = mysql_fetch_array($results)){
    // Assume there is only one row
    return $row[0];
  }
  // If we get here, nothing matched the query, so we'll insert it.
  $content = file_get_contents($url);
  $sql = "DELETE FROM content WHERE hash = '".md5($url)."'";
  mysql_query($sql);  
  $sql = "INSERT INTO content (url, age, content, hash) VALUES ('".addslashes($url)."',".time().",'".addslashes($content)."','".md5($url)."')";
  mysql_query($sql);
  return $content;
}