<?php
include("functions.php");

sec_session_start();
if(!isset($_SESSION['user_id'])){
	header("location:index.php");
}

include("config.php"); 

if (isset($_POST['action'])){	
	
	//complete
	if ($_POST['action'] == "complete"){
		if ($_SESSION['is_edging'] <> 0){
			
			$jobmaterialid = $_POST['jobmaterialid'];
			$edgingdatecompleted = date("Y-m-d H:i:s");
			$edgingcompletedby = $_SESSION['user_id'];

			$update_stmt = $mysqli->prepare("UPDATE tblJobMaterial SET EdgingDateCompleted = ?, EdgingCompletedBy = ? WHERE JobMaterialID = ?");
			$update_stmt->bind_param('ssi', $edgingdatecompleted, $edgingcompletedby, $jobmaterialid); 
			$update_stmt->execute();
					
			if ($update_stmt->affected_rows != -1){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The job was completed successfully.</div>";
			}
			else{
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be completed</div>";
			}
				
			echo json_encode($data);
		}
	}

	// save checklist and missing items
	if ($_POST['action'] == "savenotes"){
		if ($_SESSION['is_edging'] <> 0){

			$jobmaterialid = $_POST['jobmaterialid'];	
			$edgingnotes = trim($_POST['notes']);

			$update_stmt = $mysqli->prepare("UPDATE tblJobMaterial SET EdgingNotes = ? WHERE JobMaterialID = ?");
			$update_stmt->bind_param('si', $edgingnotes, $jobmaterialid); 
			$update_stmt->execute();
					
			if ($update_stmt->affected_rows != -1){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The notes were saved successfully.</div>";
			}
			else{
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The notes could not be saved</div>";
			}
				
			echo json_encode($data);
		}
	}

}
?>