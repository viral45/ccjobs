<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['is_maint'] <> 1){
	header("location:index.php");
	die();
}

include("config.php"); 

if (isset($_POST['action']))
{
	if ($_POST['action'] == "edit"){
		$maintscheduleid = isset($_POST['maintscheduleid']) ? $_POST['maintscheduleid'] : die('ERROR: MaintSchedule ID not found.');

		if ($stmt = $mysqli->prepare("SELECT MaintScheduleID, JobID, MaintJobID, Description, ScheduleDate, ScheduleDateEnd, SortOrder, Notes FROM tblMaintSchedule WHERE MaintScheduleID = ? LIMIT 1")) { 
			$stmt->bind_param('i', $maintscheduleid);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($formmaintscheduleid, $formjobid, $formmaintjobid, $formdescription, $formmaintscheduledate, $formmaintscheduledateend, $formsortorder, $formnotes);
			$stmt->fetch();
		}
	}
}
?>

<form id='maintschedule-form' action='#' method='post'>
    <input type="hidden" id="action" name="action" value="<?php if (isset($_POST['action'])) { echo $_POST['action']; } ?>">
    <input type="hidden" id="maintscheduleid" name="maintscheduleid" value="<?php if (isset($_POST['maintscheduleid'])) { echo $_POST['maintscheduleid']; } ?>">

	<div class="form-group">
		<label for="inputJobID">Job</label>
		<div class="input-group">
			<select id="inputJob" name="inputJob" class="form-control selectpicker jobgroup">
				<option value="">None</option>
				<?php
					$query = "(SELECT tblJob.JobID, tblJob.JobAddress, '' As JobType FROM tblJob INNER JOIN tblJobTaskMaint ON tblJob.JobID = tblJobTaskMaint.JobID WHERE tblJob.Deleted = 0 GROUP BY tblJob.JobID ORDER BY tblJob.JobAddress) UNION ALL (SELECT CONCAT('M', MaintJobID) As JobID, JobAddress, 'M' As JobType FROM tblMaintJob WHERE Deleted = 0 ORDER BY MaintJobID)";
					$result = $mysqli->query($query);			

					while($row = $result->fetch_array())
					{	
						$selected = "";
						if (isset($formjobid))
							$selected = ($row['JobID'] == $formjobid) ? " SELECTED" : ""; 							

						if (isset($formmaintjobid))
							$selected = ($row['JobID'] == 'M' . $formmaintjobid) ? " SELECTED" : ""; 	
						

						echo "<option value='" . $row['JobID'] . "' $selected>" . $row['JobAddress'] . "</option>";
					}
				?>
			</select>
			<span class="input-group-btn">
				<button id="viewjob-btn" class="btn btn-primary" type="button">View</button>
			</span>
		</div>

	</div>

	<div class="form-group">
		<label for="inputBuilder">Builder</label>
		<input type="text" class="form-control" id="inputBuilder" name="inputBuilder" disabled="disabled">
	</div>
		
	<div class="form-group">
		<label for="inputDescription">Description (if no linked job)</label>
		<input type="text" class="form-control jobgroup" id="inputDescription" name="inputDescription"  autocomplete="off" placeholder="Description" value="<?php if (isset($formdescription)) { echo htmlspecialchars($formdescription, ENT_QUOTES); } ?>">
	</div>
	
	<div class="form-group">
		<label for="inputMaintScheduleDate">Start</label>
		<input type="text" class="form-control" style="position: relative; z-index: 5;" id="inputMaintScheduleDate" name="inputMaintScheduleDate" required autocomplete="off" placeholder="Start" value="<?php if (isset($formmaintscheduledate)) { echo htmlspecialchars(date("d-m-Y h:i A", strtotime($formmaintscheduledate)), ENT_QUOTES); } ?>">
	</div>

	<div class="form-group">
		<label for="inputMaintScheduleDateEnd">End</label>
		<input type="text" class="form-control" style="position: relative; z-index: 5;" id="inputMaintScheduleDateEnd" name="inputMaintScheduleDateEnd" required autocomplete="off" placeholder="End" value="<?php if (isset($formmaintscheduledateend)) { echo htmlspecialchars(date("d-m-Y h:i A", strtotime($formmaintscheduledateend)), ENT_QUOTES); } ?>">
	</div>
	
	<div class="form-group">
		<label for="inputNotes">Notes</label>
		<textarea class="form-control" id="inputNotes" name="inputNotes" rows="6"><?php if (isset($formnotes)) { echo htmlspecialchars($formnotes, ENT_QUOTES); } ?></textarea>
	</div>	

    <button type="submit" id="save-btn" class="btn btn-primary">Save</button>
	<button type="button" id="return-btn" class="btn btn-warning"  data-dismiss="modal">Return</button>
	
	<?php if ($_POST['action'] == "edit"){ ?>
	<button type="button" id="delete-btn" class="btn btn-danger pull-right" value="<?php if (isset($formmaintscheduleid)) { echo $formmaintscheduleid; } ?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete</button> 
	<?php } ?>
</form>