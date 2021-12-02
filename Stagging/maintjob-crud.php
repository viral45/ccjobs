<?php
include("functions.php");

sec_session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['is_maint'] <> 1){
	header("location:index.php");
	die();
}

include("config.php"); 

if (isset($_POST['action'])){	
	
	//add a maintenance job
	if ($_POST['action'] == "add"){
	
		$userid = $_SESSION['user_id'];
		$jobaddress = $_POST['inputJobAddress'];
		$contactdetails = $_POST['inputContactDetails'];
		$dateentered = date("Y-m-d");
		$notes = $_POST['inputNotes'];
		$complete = isset($_POST['inputComplete']) ? $_POST['inputComplete'] : 0;

		$insert_stmt = $mysqli->prepare("INSERT INTO tblMaintJob (JobAddress, ContactDetails, UserID, DateEntered, Notes, Complete) VALUES (?, ?, ?, ?, ?, ?)");
		$insert_stmt->bind_param('sssssi', $jobaddress, $contactdetails, $userid, $dateentered, $notes, $complete); 
		$insert_stmt->execute();
				
		if ($insert_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The maintenance job was added successfully.</div>";
			$data['last_insert_id'] = $insert_stmt->insert_id;
			$data['action'] = "edit";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The maintenance job could not be added</div>";
			$data['action'] = "add";
		}
			
		echo json_encode($data);
	}
	
	//edit a maintenance job
	if ($_POST['action'] == "edit"){

		$maintjobid = $_POST['maintjobid'];
		$jobaddress = $_POST['inputJobAddress'];
		$contactdetails = $_POST['inputContactDetails'];
		$notes = $_POST['inputNotes'];
		$complete = isset($_POST['inputComplete']) ? $_POST['inputComplete'] : 0;

		$update_stmt = $mysqli->prepare("UPDATE tblMaintJob SET JobAddress = ?, ContactDetails = ?, Notes = ?, Complete = ? WHERE MaintJobID = ?"); 
		$update_stmt->bind_param('sssii', $jobaddress, $contactdetails, $notes, $complete, $maintjobid); 
		$update_stmt->execute();
		
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The maintenance job was updated successfully.</div>";
			$data['last_insert_id'] = $maintjobid;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The maintenance job could not be updated.</div>";
			$data['last_insert_id'] = $maintjobid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}

	

	//delete a maintschedule entry
	if ($_POST['action'] == "delete"){
		if (isset($_POST['deleteid'])){
			$deleteid = $_POST['deleteid'];

			$stmt = $mysqli->prepare("DELETE FROM tblMaintJob WHERE MaintJobID = ? LIMIT 1");
			$stmt->bind_param("i", $deleteid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected maintenance job was deleted successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected maintenance job could not be deleted</div>";
				
			$stmt->close();
		}
	}

}

?>