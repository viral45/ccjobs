<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
	die();
}

include("config.php"); 
$page = "welcome";
include("header.php"); 


?>

	<div class="page-header">
		<h1>Job Management System</h1>
	</div>

	<div class="row">
		<div class="span6 tbl_jms"> 
			<div class="section_title">
				Missing Parts & Work
			</div>
			<div id='page-content-job'>

			</div>
		</div>
		<div class="span6 tbl_jms"> 
			<div class="section_title">
				User - Active
			</div>
			<div id='page-content-user'>

			</div>
	</div>
	</div>
	<div id='loader-image'><img src='img/ajax-loader.gif' /> &nbsp;LOADING</div>	
		
<script>
	
	var currentPage = 1;
	showJobs(1);
	showUsers(1);
	function showJobs(page){
			$('#loader-image').show();
			$('#page-content-job').hide();
			
			
			$('#page-content-job').load('index-list.php', "&jobpage=" + page , function(){ 
				$('#loader-image').hide(); 
				$('#page-content-job').fadeIn('slow');
				
			});
		}

	function showUsers(page){
		$('#loader-image').show();
		$('#page-content-user').hide();
		
		
		$('#page-content-user').load('index-user-list.php', "&userpage=" + page , function(){ 
			$('#loader-image').hide(); 
			$('#page-content-user').fadeIn('slow');
			
		});
	}

</script>
<?php include("footer.php"); ?>

