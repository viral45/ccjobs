<?php
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

if (isset($_POST['action'])){	
	
	//add a Project
	if ($_POST['action'] == "add"){
		
		$name = $_POST['inputprojectName'];
		$discription = $_POST['inputprojectDiscription'];
		$dateentered = date("Y-m-d H:i:s");

		$insert_stmt = $mysqli->prepare("INSERT INTO tblProject (ProjectName,Discription,DateEntered) VALUES (?,?,?)");
		$insert_stmt->bind_param('sss', $name,$discription,$dateentered); 
		$insert_stmt->execute();
				
		if ($insert_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The project was added successfully.</div>";
			$data['last_insert_id'] = $insert_stmt->insert_id;
			$data['action'] = "edit";

			if (!empty($measureby))
				emailDraftsman($measureby, $data['last_insert_id'], $mysqli);
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The project could not be added</div>";
			$data['action'] = "add";
		}
			
		echo json_encode($data);
	}

	//edit project
	if ($_POST['action'] == "edit"){
		
		$projectid = $_POST['projectid'];
		$name = $_POST['inputprojectName'];
		$discription = $_POST['inputprojectDiscription'];


		$update_stmt = $mysqli->prepare("UPDATE tblProject SET ProjectName = ?, Discription = ? WHERE ProjectID = ?"); 
		$update_stmt->bind_param('ssi', $name, $discription, $projectid); 
		$update_stmt->execute();
		
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The Project was updated successfully.</div>";
			$data['last_insert_id'] = $projectid;

		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The Project could not be updated.</div>";
			$data['last_insert_id'] = $projectid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}

	//delete project
	if ($_POST['action'] == "delete"){
		if (isset($_POST['deleteid'])){
			$deleteid = $_POST['deleteid'];
			$deleted = 1;

			$stmt = $mysqli->prepare("UPDATE tblproject SET Deleted = ? WHERE ProjectID = ?");
			$stmt->bind_param("ii",$deleted, $deleteid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected project was deleted successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected project could not be deleted</div>";
				
			$stmt->close();
		}
	}
}