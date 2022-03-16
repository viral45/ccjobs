<?php
	include("functions.php");

	sec_session_start();
	if(!isset($_SESSION['user_id'])){
		header("location:index.php");
		die();
	}
	
	include("config.php");

	if (!empty($_REQUEST['sortorder'])){	
		$sortorder = $_REQUEST['sortorder'];
	}
	else
		$sortorder = "tblJob.JobID";
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
					<div class="panel-body">
						<form id="searchForm" class="form-horizontal" method="get" action="installer-missing.php">
							<div class="form-group">
								<div class="col-sm-3">
									<label for="sortorder">Sort By</label>
									<select id="sortorder" name="sortorder" class="form-control">
										<option value="tblJob.JobID" <?php if ($sortorder=='tblJob.JobID'){echo 'SELECTED="SELECTED"';} ?>>Job No.</option>
										<option value="tblJobTaskInstall.DateChecked" <?php if ($sortorder=='tblJobTaskInstall.DateChecked'){echo 'SELECTED="SELECTED"';} ?>>Date Checked (Oldest to Newest)</option>
										<option value="tblJobTaskInstall.DateChecked DESC" <?php if ($sortorder=='tblJobTaskInstall.DateChecked DESC'){echo 'SELECTED="SELECTED"';} ?>>Date Checked (Newest to Oldest)</option>
									</select>
								</div>              
							</div>
							
							<button type="submit" id="search-btn" class="btn btn-info">Sort</button>
							<a href="job-search.php" class="btn btn-warning">Reset</a>
						</form>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading">
						Jobs with Missing Items
					</div>
					<div class="panel-body">

						<div class="table-responsive">
			
							<?php
								$query = "SELECT Count(tblJob.JobID) FROM tblJob INNER JOIN tblJobTaskInstall ON tblJob.JobID = tblJobTaskInstall.JobID WHERE tblJob.Deleted = 0 AND (tblJobTaskInstall.MissingItems IS NOT NULL AND tblJobTaskInstall.MissingItems != '') AND tblJobTaskInstall.MissingItemsComplete = 0 AND tblJobTaskInstall.CheckedBy = '" . $_SESSION['user_id'] . "' GROUP BY tblJob.JobID";
								
								$result = $mysqli->query($query);
								$row = $result->fetch_array(); 
												
								if ($row[0] > 0 ){
							?> 
							
								<table class="table table-striped">
									<thead>
										<tr>
											<th nowrap>Job No.</th>
											<th>Address</th>
											<th>Area</th>
											<th>Checked</th>
											<th>Missing Items</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										
										<?php 
										$query = "SELECT tblJob.JobID, tblJob.JobAddress, tblTask.TaskName, tblJobTaskInstall.DateChecked, tblJobTaskInstall.CheckedBy, tblUser.FullName, tblJobTaskInstall.DateCompleted, tblJobTaskInstall.CompletedBy, tblJobTaskInstall.MissingItems FROM ((tblJob INNER JOIN tblJobTaskInstall ON tblJob.JobID = tblJobTaskInstall.JobID) INNER JOIN tblTask ON tblJobTaskInstall.TaskID = tblTask.TaskID) INNER JOIN tblUser ON tblJobTaskInstall.CheckedBy = tblUser.UserID WHERE tblJob.Deleted = 0 AND (tblJobTaskInstall.MissingItems IS NOT NULL AND tblJobTaskInstall.MissingItems != '') AND tblJobTaskInstall.MissingItemsComplete = 0 AND tblJobTaskInstall.CheckedBy = '" . $_SESSION['user_id'] . "' ORDER BY $sortorder";
										$result = $mysqli->query($query);
										
										$totaljobs = 0;
										while($row = $result->fetch_array()){
											
											$totaljobs++;
										?>
											<tr>
												<td ><?php echo $row['JobID'] ?></td>
												<td><?php echo $row['JobAddress'] ?></td>
												<td><?php echo $row['TaskName'] ?></td>
												<td><?php if (!empty($row['DateChecked'])){echo date("d-m-Y", strtotime($row['DateChecked'])) . " by " . $row['FullName']; } ?></td>
												<td><?php echo nl2br($row['MissingItems']); ?></td>
												<td nowrap>   
													<a href="job.php?jobid=<?php echo $row['JobID'] . "#installer"; ?>" class="btn btn-primary btn-xs edit-btn"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
												</td>
											</tr>
											
										<?php } ?>
										
										<tr class="warning">
											<td colspan="8"><strong>TOTAL JOBS: <?php echo $totaljobs; ?></strong></td>
										</tr>

									</tbody>   
								</table>
								
							<?php } else { echo "No records were found."; } ?>
							
						</div>
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