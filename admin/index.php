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
$data = "SELECT tblJobTask.*,tblUser.FullName FROM tblJobTask INNER JOIN tblUser ON tblUser.UserID = tblJobTask.StartedBy WHERE tblJobTask.MissingItemsComplete = 0 AND tblJobTask.MissingItems != '' AND tblJobTask.MissingItems IS NOT NULL ";
$result = $mysqli->query($data);

?>

	<div class="page-header">
		<h1>Job Management System</h1>
	</div>

		<?php
			$count = 0;
			while($row = $result->fetch_array())
			{
		?>	
				<div class="row">

				<?php
					if($count == 0){
						
				?>
					<div class="span6 tbl_jms"> 
						<div class="section_title">
							Missing Parts & Work
						</div>
					</div>
					<div class="span6 tbl_jms"> 
						<div class="section_title">
							User - Active
						</div>
					</div>
			
				<?php
					}	
				?>

					<div class="span6 tbl_jms">
						<p class="tbl_jms_id"><a href="../job.php?jobid=<?php echo $row['JobID']; ?>#draftsman">Job# |<?php echo $row['JobID']; ?></a></p>
						<p class="tbl_jms_missingitems"><?php echo $row['MissingItems']; ?></p>
					</div>
					<div class="span6 tbl_jms">
						<p class="job_user">User# |  <?php echo $row['StartedBy']; ?>  <?php echo $row['FullName']; ?></p>
						<div class="job_action_btn">
							<a href="../my-jobs.php" type="button" class="btn btn-success">Jobs</a>
							<a href="../drawer-schedule.php" type="button" class="btn btn-success">Schedule</a>
							<a href="../installer-missing.php" type="button" class="btn btn-success">Missing</a>
							<a href="#" type="button" class="btn btn-success">Edit</a>
						</div>
					</div>
				</div>
			
		<?php 
			$count++;
			}
		?>
		

<?php include("footer.php"); ?>