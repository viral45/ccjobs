<?php
	include("functions.php");

	sec_session_start();
	if(!isset($_SESSION['user_id']) || $_SESSION['is_maint'] <> 1){
		header("location:index.php");
		die();
	}
	
	include("config.php");

	if ($_GET['action'] == "edit"){
		$maintjobid = isset($_GET['maintjobid']) ? (int)$_GET['maintjobid'] : die('ERROR: Job Number not found.');

		if ($stmt = $mysqli->prepare("SELECT MaintJobID, JobAddress, ContactDetails, UserID, DateEntered, Notes, Complete FROM tblMaintJob WHERE MaintJobID = ? LIMIT 1")) { 
			$stmt->bind_param('i', $maintjobid);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($formmaintjobid, $formjobaddress, $formcontactdetails, $formuserid, $formdateentered, $formnotes, $formcomplete);
			$stmt->fetch();
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
    <link href="css/style.css?v=1.1" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">
			<div class="row">
				<div class="col-xs-6 col-sm-4 col-md-3">
					<img src="img/logo_large.png" class="img-responsive" alt="Challenge Cabinets">
					<br>
				</div>
				<div class="col-sm-8 col-md-9">
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
							<div id="alert"></div>
							<div id='page-content'>
								<form id='maintjob-form' action='#' method='post'>
									<input type="hidden" id="action" name="action" value="<?php if (isset($_GET['action'])) { echo $_GET['action']; } ?>">
									<input type="hidden" id="maintjobid" name="maintjobid" value="<?php if (isset($_GET['maintjobid'])) { echo $_GET['maintjobid']; } ?>">

									<?php if ($_GET['action'] == "edit"){ ?>
										<div class="form-group">
											<label for="inputMaintJobID">Job Number</label>
											<input type="text" class="form-control" id="inputMaintJobID" name="inputMaintJobID" placeholder="Job Number" disabled value="<?php if (isset($formmaintjobid)) { echo 'M' . htmlspecialchars($formmaintjobid, ENT_QUOTES); } ?>">
										</div>

										<div class="form-group">
											<label for="inputDateEntered">Date Entered</label>
											<input type="text" class="form-control" id="inputDateEntered" name="inputDateEntered" placeholder="Date Entered" disabled value="<?php if (isset($formdateentered)) { echo htmlspecialchars(date('d-m-Y', strtotime($formdateentered)), ENT_QUOTES); } ?>">
										</div>
									<?php } ?>

									<div class="form-group">
										<label for="inputJobAddress">Address</label>
										<div class="input-group">
											<input type="text" class="form-control" id="inputJobAddress" name="inputJobAddress" placeholder="Address" required value="<?php if (isset($formjobaddress)) { echo htmlspecialchars($formjobaddress, ENT_QUOTES); } ?>" autocomplete="off">
											<span class="input-group-btn">
												<button type="button" class="btn btn-danger" id="map-btn"><span class="glyphicon glyphicon-map-marker" aria-hidden="true" ></span> Map</button>
											</span>
										</div>
											
									</div>
								
									<div class="form-group">
										<label for="inputContactDetails">Contact Details</label>
										<input type="text" class="form-control" id="inputContactDetails" name="inputContactDetails" placeholder="Contact Details" value="<?php if (isset($formcontactdetails)) { echo htmlspecialchars($formcontactdetails, ENT_QUOTES); } ?>" autocomplete="off">
									</div>   
								
									<div class="form-group">
										<label for="inputNotes">Notes</label>
										<textarea class="form-control" id="inputNotes" name="inputNotes" rows="6"><?php if (isset($formnotes)) { echo htmlspecialchars($formnotes, ENT_QUOTES); } ?></textarea>
									</div>	
								
									<div class="form-group">
										<label>
											<input name="inputComplete" type="checkbox" value="1" <?php if (isset($formcomplete)) { if ($formcomplete==1){ echo " CHECKED"; } } ?>> Complete
										</label>
									</div>

									<button type="submit" id="save-btn" class="btn btn-primary">Save</button>
									<a href="maint-jobs.php" class="btn btn-warning">Return</a>
									
									<?php if ($_GET['action'] == "edit"){ ?>
									<button type="button" id="delete-btn" class="btn btn-danger pull-right" value="<?php if (isset($formmaintjobid)) { echo $formmaintjobid; } ?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete</button> 
									<?php } ?>
								
								</form>
							</div>
							<div id='loader-image'><img src='img/ajax-loader.gif' /> &nbsp;LOADING</div>
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
	<script src="js/bootstrap-filestyle.min.js"></script>

	<script type='text/javascript'>

		$('form').on('submit', function(e){
			e.preventDefault();
		});
		
		$(document).ready(function(){
			
			$('#alert').hide();
			$('#page-content').show();
			$('#loader-image').hide();

			$.validator.setDefaults({
				highlight: function(element) {
					$(element).parent().addClass('has-error');
				},
				unhighlight: function(element) {
					$(element).parent().removeClass('has-error');
				},
				errorElement: 'div',
				errorClass: 'help-block',
				errorPlacement: function(error, element) {
					if(element.parent('.input-group').length) {
						element.parent().parent().append(error);
					} else {
						element.parent().append(error);
					}
				}
			});
				
			$('form').each(function() {
				$(this).validate();
			});

			$(document).on('submit', '#maintjob-form', function() {
				$('#alert').hide();
				$('#page-content').hide();
				$('#loader-image').show();

				$.post("maintjob-crud.php", $(this).serialize())
					.done(function(data) {
						var response = jQuery.parseJSON(data);
						$('#alert').html(response.msg);
						$('#alert').show();
						$('#page-content').show();
						$('#loader-image').hide();
						//location.reload();
					});
						
				return false;
			});

			$('#delete-btn').click(function(){
				deleteJob($(this).val());
			});

			$('#map-btn').click(function(){
				var searchstring = encodeURIComponent($("#inputJobAddress").val());
				window.open("https://www.google.com/maps?q="+searchstring);
			});

			function deleteJob(deleteid){
				$.confirm({
					text: "Are you sure you want to delete this job?",
					confirm: function() {
						$('#page-content').hide();
						$('#loader-image').show();
						
						$.post("maintjob-crud.php", { action: 'delete', deleteid: deleteid }) 
							.done(function(data){
								window.location.href = 'maint-jobs.php';
						});
					},
					cancel: function() {
						// nothing to do
					}
				});
			}
			
		});
	</script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>