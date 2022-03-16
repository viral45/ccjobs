<?php
	include("functions.php");

	sec_session_start();
	if(!isset($_SESSION['user_id'])){
		header("location:index.php");
		die();
	}
	
	include("config.php");

	$weekstart = (!empty($_REQUEST["weekstart"])) ? $_REQUEST["weekstart"] : date('d-m-Y');

	$mondaydate = date('d-m-Y',strtotime("monday this week", strtotime($weekstart)));
	$tuesdaydate = date('d-m-Y',strtotime("tuesday this week", strtotime($weekstart)));
	$wednesdaydate = date('d-m-Y',strtotime("wednesday this week", strtotime($weekstart)));
	$thursdaydate = date('d-m-Y',strtotime("thursday this week", strtotime($weekstart)));
	$fridaydate = date('d-m-Y',strtotime("friday this week", strtotime($weekstart)));
	
	$datearray = array($mondaydate, $tuesdaydate, $wednesdaydate, $thursdaydate, $fridaydate);

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
    <link href="css/theme.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
  	<?php
  	
  		if(isset($_SESSION['logged_in'])){
  			include("header.php");
  		} 
  	?>
    <div class="container">
		<div class="row">
			<div class="col-xs-9 col-sm-3">
				<img src="img/logo_large.png" class="img-responsive" alt="Challenge Cabinets">
				<br>
			</div>
			<div class="col-sm-9">
				<ul class="nav nav-pills pull-right">
					<li role="presentation"><a href="index.php">Home</a></li>
					<li role="presentation"><a href="job-lookup.php">Change Job</a></li>
					<li role="presentation"><a href="job-search.php">Search</a></li>
				</ul>
				
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="well">				
						Current User: <strong><?php echo $_SESSION['full_name'] ?></strong>
				</div>
				
				<div class="panel panel-default">
					<div class="panel-heading">
						Draftsman Schedule
					</div>
					<div class="panel-body">
						<div >
							
							<?php echo "<h3>WEEK " . $mondaydate . " to " . $fridaydate . "</h3>"; ?>
							<table class="table table-bordered table-striped">
								<thead>
									<tr>
										<th style="width: 20%">Day</th>
										<th style="width: 80%">Jobs</th>
									</tr>
								</thead>

								<tbody>
								 
									
									<?php 
										foreach ($datearray as $day){ 
											echo "<tr><td><strong>" . date('l',strtotime($day)) . "</strong><br>" . date('d-m-Y',strtotime($day)) . "</td><td>";
											
											$schedulequery = "SELECT tblDrawerSchedule.DrawerScheduleID, tblJob.JobAddress, tblJob.Builder, tblJob.JobID, tblDrawerSchedule.Description, tblDrawerSchedule.Notes FROM tblJob RIGHT JOIN tblDrawerSchedule ON tblJob.JobID = tblDrawerSchedule.JobID WHERE ScheduleDate = '" . date('Y-m-d',strtotime($day)) . "' AND UserID = '" . $_SESSION['user_id'] ."' ORDER BY SortOrder";
											$scheduleresult = $mysqli->query($schedulequery);
											
											while($schedulerow = $scheduleresult->fetch_array()){
												if (!empty($schedulerow['JobID']))
													echo "<div class='well'><strong>Job No:</strong> " . $schedulerow['JobID'] . "<br><strong>Address:</strong> " . $schedulerow['JobAddress'];
												else
													echo "<div class='well'><strong>Details:</strong> " . $schedulerow['Description'];

												if (!empty($schedulerow['Builder']))
													echo "<br><strong>Builder:</strong> " . $schedulerow['Builder'];

												if (!empty($schedulerow['Notes']))
													echo "<br><strong>Notes:</strong> " . $schedulerow['Notes'];
												
												if (!empty($schedulerow['JobID']))
													echo '<br><a href="job.php?jobid=' . $schedulerow['JobID'] . '#draftsman" class="btn btn-primary btn-xs edit-btn">View Job</a>';
													
												echo "</div>";
											}
											
											echo "</td></tr>";
											
									?>

									<?php } ?>
										
										

								</tbody>

							</table>
							
						</div>


						<a class='btn btn-primary week-btn' href="drawer-schedule.php?weekstart=<?php echo date('d-m-Y',strtotime("monday last week", strtotime($weekstart))) ?>">
							<span aria-hidden="true">&laquo;</span> Previous Week
						</a>

						<a class='btn btn-primary week-btn' href="drawer-schedule.php?weekstart=<?php echo date('d-m-Y',strtotime("monday next week", strtotime($weekstart))) ?>">
							Next Week <span aria-hidden="true">&raquo;</span>
						</a>

					</div>
				</div>

			</div>
		</div>
    </div> <!-- /container -->

    <script src="js/jquery-1.11.3.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/additional-methods.min.js"></script>
    <script src="js/jquery.confirm.min.js"></script>

	<script type='text/javascript'>

		$('form').on('submit', function(e){
			//e.preventDefault();
		});
		
		$(document).ready(function(){

			
		});

	</script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>