<?php
	include("functions.php");

	sec_session_start();
	if(!isset($_SESSION['user_id'])){
		header("location:index.php");
		die();
	}

	include("config.php");	

	if(isset($_POST['inputJobID'])) { 
	   $jobid = $_POST['inputJobID'];

	   if(findjob($jobid, $mysqli) == true)
		  header('Location: job.php?jobid='.$jobid.get_url_hash());
	   else
		  $message = "Job Number not found!";
	} 

	function findjob($jobid, $mysqli) {

	   if ($stmt = $mysqli->prepare("SELECT JobID FROM tblJob WHERE JobID = ? AND Deleted=0 LIMIT 1")) { 
			$stmt->bind_param('i', $jobid);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($jobid);
			$stmt->fetch();
			
		  if($stmt->num_rows == 1)
			return true;    
		  else 
			 return false;
	   }
	}
	
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/favicon.ico">

    <title>Challenge Cabinets Job Management System</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">

      <form class="form-keypad" method="post" action="job-lookup.php">
        <img src="img/logo_large.png" class="img-responsive" alt="Challenge Cabinets">
        <br />
        <?php if (isset($message)){?><div class="alert alert-danger" role="alert"><?php echo $message ?></div><?php } ?>

			<div class="well">User: <strong><?php echo $_SESSION['full_name'] ?></strong> <a href="index.php">(Change)</a></div>

			<div class="form-group">
        		<label for="inputJobID">Job Number</label>
        		<input type="number" id="inputJobID" name="inputJobID" class="form-control" autocomplete="off" required autofocus>
			</div>
				
			<div id="keypad" class="form-group">
				<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="1">1</button>
				<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="2">2</button>
				<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="3">3</button>
				<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="4">4</button>
				<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="5">5</button>
				<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="6">6</button>
				<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="7">7</button>
				<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="8">8</button>
				<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="9">9</button>
				<button class="reset-btn col-xs-4 btn btn-lg btn-danger" type="reset">CLEAR</button>
				<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="0">0</button>
				<button class="col-xs-4 btn btn-lg btn-primary" type="submit">ENTER</button>
			</div>
      </form>

    </div> <!-- /container -->
	<div class="container">

		<div class="form-group text-center">
			<br />
			<?php
				if ($_SESSION['is_draftsman'] <> 0){
					echo '<p><a href="my-jobs.php">My Jobs ></a></p>';
					echo '<p><a href="drawer-schedule.php">My Schedule ></a></p>';
				}

				if ($_SESSION['is_installer'] <> 0){
					echo '<p><a href="my-schedule.php">My Schedule ></a></p>';
				}

				if ($_SESSION['is_installer'] <> 0){
					echo '<p><a href="installer-missing.php">My Missing Items ></a></p>';
				}
			?>			
			<p><a href="job-search.php">Search for a job ></a></p>
		</div>
	</div>

    <script src="js/jquery-1.11.3.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/additional-methods.min.js"></script>

	<script type='text/javascript'>

		$('form').on('submit', function(e){
			//e.preventDefault();
		});
		
		$(document).ready(function(){

			$.validator.setDefaults({
				highlight: function(element) {
					$(element).closest('.form-group').addClass('has-error');
				},
				unhighlight: function(element) {
					$(element).closest('.form-group').removeClass('has-error');
				},
				errorElement: 'span',
				errorClass: 'help-block',
				errorPlacement: function(error, element) {
					if(element.parent('.input-group').length) {
						error.insertAfter(element.parent());
					} else {
						error.insertAfter(element);
					}
				}
			});

			var validator = $('.form-keypad').validate();

			$(document).on('click', '.num-btn', function(){ 
				$('#inputJobID').val($('#inputJobID').val() + $(this).val());
			});

			$(document).on('click', '.reset-btn', function(){ 
				$('.alert').hide();
				validator.resetForm();
			});
		});

	</script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>