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

/**
 * Setup:
 *
CREATE TABLE `content` (
  `url` text NOT NULL,
  `age` int(11) NOT NULL,
  `content` longblob NOT NULL,
  `hash` varchar(32) NOT NULL,
  PRIMARY KEY  (`hash`),
  KEY `age` (`age`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 */

// Get the parameters and decode them
$url = $_GET['url'];
if(isset($_GET['age'])){
  $age = $_GET['age'];
}else{
  $age = 60*60*24*7;
}

// If you want to include this script in a project, comment out the following line, and 
// use the function fencedine_get_content.
echo trim( fencedine_get_content($url, $age) );

function fencedine_get_content($url, $age){
  /**
   * The following lines should be changed according to your requirements
   */  
  ini_set('user_agent', 'Scatchpad Bot http://scratchpads.eu/bot');
  $link = mysql_connect('localhost','fencedine','vbjGFt3D7NqGTeJA');
  mysql_select_db('fencedine');
  mysql_query("SET NAMES 'utf8'");
  
  // Select the content from the database.
  $sql = "SELECT content FROM content WHERE hash='".md5($url)."' AND age > ".(time()-$age);
  $results = mysql_query($sql);
  if(!$results){
    // We've got an error, panic!
    trigger_error("Query failed: ".$sql);
  }
  while($row = mysql_fetch_array($results)){
    // Assume there is only one row
    return $row[0];
  }
  // If we get here, nothing matched the query, so we'll insert it.
  $sql = "DELETE FROM content WHERE hash = '".md5($url)."'";
  mysql_query($sql);
  $sql = "INSERT INTO content (url, age, content, hash) VALUES ('".addslashes($url)."',".time().",'".addslashes(file_get_contents($url))."','".md5($url)."')";
  mysql_query($sql);
  return fencedine_get_content($url,$age+1000);
}