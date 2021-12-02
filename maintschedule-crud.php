<?php
include("functions.php");

sec_session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['is_maint'] <> 1){
	header("location:index.php");
	die();
}

include("config.php"); 

if (isset($_POST['action'])){	
	
	//add a maintschedule entry
	if ($_POST['action'] == "add"){
	
		if (!empty($_POST['userid']))
			$userid = $_POST['userid'];
		else
			$userid = $_SESSION['user_id'];

		//$job = (!empty($_POST['inputJob'])) ? $_POST['inputJob'] : NULL;

		if (!empty($_POST['inputJob'])){
			$job = $_POST['inputJob'];

			if (substr($job, 0, 1) == 'M'){
				$maintjobid = ltrim($job, 'M');
				$jobid = NULL;
			}
			else{
				$jobid = $job;
				$maintjobid = NULL;
			}
		}

		$description = $_POST['inputDescription'];
		$maintscheduledate = (!empty($_POST['inputMaintScheduleDate'])) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $_POST['inputMaintScheduleDate']))) : NULL;
		$maintscheduledateend = (!empty($_POST['inputMaintScheduleDateEnd'])) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $_POST['inputMaintScheduleDateEnd']))) : NULL;
		$notes = $_POST['inputNotes'];

		$insert_stmt = $mysqli->prepare("INSERT INTO tblMaintSchedule (UserID, JobID, MaintJobID, Description, ScheduleDate, ScheduleDateEnd, Notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$insert_stmt->bind_param('siissss', $userid, $jobid, $maintjobid, $description, $maintscheduledate, $maintscheduledateend, $notes); 
		$insert_stmt->execute();
				
		if ($insert_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The maintenance schedule entry was added successfully.</div>";
			$data['last_insert_id'] = $insert_stmt->insert_id;
			$data['action'] = "edit";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The maintenance schedule entry could not be added</div>";
			$data['action'] = "add";
		}
			
		echo json_encode($data);
	}
	
	//edit a maintschedule entry
	if ($_POST['action'] == "edit"){

		$maintscheduleid = $_POST['maintscheduleid'];
		//$jobid = (!empty($_POST['inputJobID'])) ? $_POST['inputJobID'] : NULL;
		if (!empty($_POST['inputJob'])){
			$job = $_POST['inputJob'];

			if (substr($job, 0, 1) == 'M'){
				$maintjobid = ltrim($job, 'M');
				$jobid = NULL;
			}
			else{
				$jobid = $job;
				$maintjobid = NULL;
			}
		}

		$description = $_POST['inputDescription'];
		$maintscheduledate = (!empty($_POST['inputMaintScheduleDate'])) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $_POST['inputMaintScheduleDate']))) : NULL;
		$maintscheduledateend = (!empty($_POST['inputMaintScheduleDateEnd'])) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $_POST['inputMaintScheduleDateEnd']))) : NULL;
		$notes = $_POST['inputNotes'];

		$update_stmt = $mysqli->prepare("UPDATE tblMaintSchedule SET JobID = ?, MaintJobID = ?, Description = ?, ScheduleDate = ?, ScheduleDateEnd = ?, Notes = ? WHERE MaintScheduleID = ?"); 
		$update_stmt->bind_param('iissssi', $jobid, $maintjobid, $description, $maintscheduledate, $maintscheduledateend, $notes, $maintscheduleid); 
		$update_stmt->execute();
		
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The maintenance schedule entry was updated successfully.</div>";
			$data['last_insert_id'] = $maintscheduleid;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The maintenance schedule entry could not be updated.</div>";
			$data['last_insert_id'] = $maintscheduleid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}

	//move a maintschedule entry
	if ($_POST['action'] == "move"){
		
		$maintscheduleid = $_POST['maintscheduleid'];
		$maintscheduledate = (!empty($_POST['maintscheduledate'])) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $_POST['maintscheduledate']))) : NULL;
		$maintscheduledateend = (!empty($_POST['maintscheduledateend'])) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $_POST['maintscheduledateend']))) : NULL;

		$update_stmt = $mysqli->prepare("UPDATE tblMaintSchedule SET ScheduleDate = ?, ScheduleDateEnd = ? WHERE MaintScheduleID = ?"); 
		$update_stmt->bind_param('ssi', $maintscheduledate, $maintscheduledateend, $maintscheduleid); 
		$update_stmt->execute();
		
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The maintenance schedule entry was updated successfully.</div>";
			$data['last_insert_id'] = $maintscheduleid;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The maintenance schedule entry could not be updated.</div>";
			$data['last_insert_id'] = $maintscheduleid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}

	//sort maintschedule entries
	if ($_POST['action'] == "sort"){
		if (isset($_POST['sortorder'])){
			$sortorder = $_POST['sortorder'];

			$sort = 1;
			foreach ($sortorder as $maintscheduleid){
				$update_stmt = $mysqli->prepare("UPDATE tblMaintSchedule SET SortOrder = ? WHERE MaintScheduleID = ?"); 
				$update_stmt->bind_param('ii', $sort, $maintscheduleid); 
				$update_stmt->execute();

				$sort++;
			}
		}
	}

	//delete a maintschedule entry
	if ($_POST['action'] == "delete"){
		if (isset($_POST['deleteid'])){
			$deleteid = $_POST['deleteid'];

			$stmt = $mysqli->prepare("DELETE FROM tblMaintSchedule WHERE MaintScheduleID = ? LIMIT 1");
			$stmt->bind_param("s", $deleteid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected maintenance schedule entry was deleted successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected maintenance schedule entry could not be deleted</div>";
				
			$stmt->close();
		}
	}

	//get job builder
	if ($_POST['action'] == "getbuilder"){
		if (isset($_POST['jobid'])){
			$jobid = $_POST['jobid'];
			
			if ($stmt = $mysqli->prepare("SELECT Builder FROM tblJob WHERE JobID = ? LIMIT 1")) { 
				$stmt->bind_param('i', $jobid);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($builder);
				$stmt->fetch();
			}

			if (isset($builder))
				echo $builder;

		}
	}

	
}

if (isset($_GET['action'])){	

	if ($_GET['action'] == "list"){
		$eventarray = array();
		$startdate = $_GET['start'];
		$enddate = $_GET['end'];
		$maintuserid = $_GET['userid'];

		$maintschedulequery = "(SELECT tblMaintSchedule.MaintScheduleID, tblJob.JobAddress, tblJob.JobID, tblMaintSchedule.Description, tblMaintSchedule.SortOrder As Sort, tblMaintSchedule.ScheduleDate, tblMaintSchedule.ScheduleDateEnd FROM tblJob INNER JOIN tblMaintSchedule ON tblJob.JobID = tblMaintSchedule.JobID WHERE tblMaintSchedule.UserID = $maintuserid AND tblMaintSchedule.ScheduleDate BETWEEN '" . date('Y-m-d',strtotime($startdate)) . "' AND '" . date('Y-m-d',strtotime($enddate)) . "') UNION ALL (SELECT tblMaintSchedule.MaintScheduleID, tblMaintJob.JobAddress, tblMaintJob.MaintJobID, tblMaintSchedule.Description, tblMaintSchedule.SortOrder As Sort, tblMaintSchedule.ScheduleDate, tblMaintSchedule.ScheduleDateEnd FROM tblMaintJob INNER JOIN tblMaintSchedule ON tblMaintJob.MaintJobID = tblMaintSchedule.MaintJobID WHERE tblMaintSchedule.UserID = $maintuserid AND tblMaintSchedule.ScheduleDate BETWEEN '" . date('Y-m-d',strtotime($startdate)) . "' AND '" . date('Y-m-d',strtotime($enddate)) . "') UNION ALL (SELECT MaintScheduleID, '' As JobAddress, '' As JobID, Description, SortOrder As Sort, ScheduleDate, ScheduleDateEnd FROM tblMaintSchedule WHERE JobID IS NULL and MaintJobID IS NULL AND tblMaintSchedule.UserID = $maintuserid AND ScheduleDate BETWEEN '" . date('Y-m-d',strtotime($startdate)) . "' AND '" . date('Y-m-d',strtotime($enddate)) . "') ORDER BY Sort";
		//echo $maintschedulequery;
		
		$maintscheduleresult = $mysqli->query($maintschedulequery);
		
		while($maintschedulerow = $maintscheduleresult->fetch_array()){

			if (!empty($maintschedulerow['JobID'])){
				$eventarray[] = array('id' => $maintschedulerow['MaintScheduleID'], 'title' => $maintschedulerow['JobAddress'], 'start' => $maintschedulerow['ScheduleDate'], 'end' => $maintschedulerow['ScheduleDateEnd']);
			}
			else{
				$eventarray[] = array('id' => $maintschedulerow['MaintScheduleID'], 'title' => $maintschedulerow['Description'], 'start' => $maintschedulerow['ScheduleDate'], 'end' => $maintschedulerow['ScheduleDateEnd']);
				
				//echo "<div class='alert alert-warning calendar-entry' data-action='edit' data-maintschedule-id='" . $maintschedulerow['MaintScheduleID'] . "'><button type='button' class='close delete-btn' aria-label='Close' value='" . $maintschedulerow['MaintScheduleID'] . "'><span aria-hidden='true'>&times;</span></button>" . $maintschedulerow['Description'] . "</div>";                                    
			}                           
		}
		echo json_encode($eventarray);
	}
}
?>