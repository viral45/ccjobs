<?php 
    include("functions.php");

    sec_session_start();
    if(!isset($_SESSION['user_id'])){
        header("location:index.php");
        die();
    }

    include("config.php");

	$jobid = isset($_REQUEST['jobid']) ? (int)$_REQUEST['jobid'] : die('ERROR: Job Number not found.');
	
	if ($_SESSION['is_installer'] <> 0){
?>
		<form id='job-photo-form' action='#' class="form-horizontal" method='post' enctype="multipart/form-data">

			<input type="hidden" id="jobid" name="jobid" value="<?php if (isset($_POST['jobid'])) { echo $_POST['jobid']; } ?>">

			<div class="form-group">
				<div class="col-sm-12">
					<label for="inputFileLocation">Add Photos (JPG only)</label>
					<input type="file" accept='image/*' multiple="multiple" class="form-control" id="inputFileLocation" name="inputFileLocation">
				</div>
			</div>
			
			<button type="button" id="add-photos-btn" class="btn btn-primary"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload</button>
		</form>

		<br /><br />

<?php
	}

	$query = "SELECT COUNT(JobUploadID) FROM tblJobUpload WHERE JobID = $jobid AND IsPhoto <> 0";
	$result = $mysqli->query($query);
	$row = $result->fetch_array(); 

	if ($row[0] > 0 ){

	?>
		<strong>Existing Photos</strong>
		<br /><br />
		<div class="row">
	
	<?php
        $query = "SELECT * FROM tblJobUpload WHERE JobID = $jobid And IsPhoto <> 0 ORDER BY JobUploadID";
        $result = $mysqli->query($query);

        while($row = $result->fetch_array()){
        ?>
        
		
			<div class="col-xs-6 col-md-3">
				<div class="thumbnail">
					<a href="<?php echo $uploadpath . $row['FilePath'] ?>" target="_blank">
						<img src="<?php echo $uploadpath . "thumbs/" . $row['FilePath'] ?>" alt="">
					</a>
					<?php
						if ($_SESSION['is_installer'] <> 0){
					?>
							<div class="caption text-center">
								<button type="button" value="<?php echo $row['JobUploadID']; ?>" class="btn btn-danger btn-sm delete-photo-btn"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
							</div>
						<?php } ?>
				</div>
			</div>
		
		
        <?php } ?>
    </div>
<?php 
	
	} else { echo "<p>There are no existing photos for this job.</p>"; } ?>
    