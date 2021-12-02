<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
	die();
}

include("config.php"); 

$page = "home";

include("header.php"); 

?>

<div class="page-header">
<h1>Restricted Page</h1>
</div>
<div id='page-content'>Sorry, you do not have access to this page.</div>


<?php include("footer.php"); ?>