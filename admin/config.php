<?php

define("HOST", "127.0.0.1"); 
define("USER", "root");
define("PASSWORD", "");
define("DATABASE", "jobsdb");

// define("HOST", "127.0.0.1"); 
// define("USER", "challeng_jobsusr");
// define("PASSWORD", "fKcqW0b8pb3g");
// define("DATABASE", "challeng_jobsdb");

$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);

//Output any connection error
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}

$sitename = "Challenge Cabinets"; 

$recordsperpage = 25;

date_default_timezone_set('Australia/Perth');

function array_stripslash($theArray){
   foreach ( $theArray as &$v ) if ( is_array($v) ) $v = array_stripslash($v); else $v = stripslashes($v);
   return $theArray;
}

if ( !empty($_GET) ) $_GET = array_stripslash($_GET);
if ( !empty($_POST) ) $_POST = array_stripslash($_POST);

?>
