<?php 
    include("functions.php");

    sec_session_start();
    if(!isset($_SESSION['user_id'])){
        header("location:index.php");
        die();
    }

    include("config.php");

	$jobid = isset($_REQUEST['jobid']) ? (int)$_REQUEST['jobid'] : die('ERROR: Job Number not found.');
	
	if ($_SESSION['is_draftsman'] <> 0){
?>
		<form id='job-photo-form' action='#' class="form-horizontal" method='post' enctype="multipart/form-data">

			<input type="hidden" id="jobid" name="jobid" value="<?php if (isset($_POST['jobid'])) { echo $_POST['jobid']; } ?>">

			<div class="form-group">
				<div class="col-sm-12">
					<label for="inputFileLocation">Add Plans (PDF only)</label>
					<input type="file" accept='application/pdf' multiple="multiple" class="form-control" id="inputFileLocation" name="inputFileLocation">
				</div>
			</div>
			
			<button type="button" id="add-plans-btn" class="btn btn-primary"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload</button>
		</form>

		<br /><br />

<?php
	}

	$query = "SELECT COUNT(JobUploadID) FROM tblJobUpload WHERE JobID = $jobid AND IsPlan <> 0";
	$result = $mysqli->query($query);
	$row = $result->fetch_array(); 

	if ($row[0] > 0 ){

	?>
		<strong>Existing Plans (click to view)</strong>
		<br /><br />
		<div class="row">
			<div class="col-xs-12">
				<table class="table table-striped">
					<tbody>
					<?php
						$query = "SELECT * FROM tblJobUpload WHERE JobID = $jobid And IsPlan <> 0 ORDER BY JobUploadID";
						$result = $mysqli->query($query);

						while($row = $result->fetch_array()){
						?>
        
						<tr>
							<td>
								<a href="<?php echo $uploadpath . $row['FilePath'] ?>" target="_blank"><u><?php echo $row['FilePath']; ?></u></a>
							</td>
							<td class="text-right">
								<?php
									if ($_SESSION['is_draftsman'] <> 0){
								?>
										<button type="button" value="<?php echo $row['JobUploadID']; ?>" class="btn btn-danger btn-sm delete-plan-btn"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
								<?php } ?>
							</td>
						</tr>
			
        			<?php } ?>
					</tbody>
				</table>
			</div>
    	</div>
<?php 
	
	} else { echo "<p>There are no existing plans for this job.</p>"; } ?>
    