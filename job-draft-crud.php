<?php
include("functions.php");

sec_session_start();
if(!isset($_SESSION['user_id'])){
	header("location:index.php");
}

include("config.php"); 

if (isset($_POST['action'])){	

	//start a job
	if ($_POST['action'] == "start"){
		if ($_SESSION['is_draftsman'] <> 0){
			$jobid = $_POST['jobid'];
			//$taskid = $_POST['taskid'];
			$datestarted = date("Y-m-d H:i:s");
			$startedby = $_SESSION['user_id'];

			$query = "SELECT TaskID FROM tblTask WHERE Weight < 2 ORDER BY TaskID";
			$result = $mysqli->query($query);

			while($row = $result->fetch_array()){
				$taskid = $row['TaskID'];

				$insert_stmt = $mysqli->prepare("INSERT INTO tblJobTaskDraft (JobID, TaskID, DateStarted, StartedBy) VALUES (?, ?, ?, ?)");
				$insert_stmt->bind_param('iiss', $jobid, $taskid, $datestarted, $startedby); 
				$insert_stmt->execute();
			}	
					
			if ($insert_stmt->affected_rows != -1){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The job was started successfully.</div>";
			}
			else{
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be started</div>";
			}
				
			echo json_encode($data);
		}
	}

	//drawn
	if ($_POST['action'] == "drawn"){
		if ($_SESSION['is_draftsman'] <> 0){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];
			$datedrawn = date("Y-m-d H:i:s");
			$drawnby = $_SESSION['user_id'];

			$update_stmt = $mysqli->prepare("UPDATE tblJobTaskDraft SET DateDrawn = ?, DrawnBy = ? WHERE JobID = ? AND TaskID = ?");
			$update_stmt->bind_param('ssii', $datedrawn, $drawnby, $jobid, $taskid); 
			$update_stmt->execute();
					
			if ($update_stmt->affected_rows != -1){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The job was marked as drawn successfully.</div>";
			}
			else{
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be marked as drawn</div>";
			}
				
			echo json_encode($data);
		}
	}

	//complete
	if ($_POST['action'] == "complete"){
		if ($_SESSION['is_draftsman'] <> 0){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];
			$datecompleted = date("Y-m-d H:i:s");
			$completedby = $_SESSION['user_id'];
			$is_off = 1;

			$update_stmt = $mysqli->prepare("UPDATE tblJobTaskDraft SET DateCompleted = ?, CompletedBy = ?,is_off = ? WHERE JobID = ? AND TaskID = ?");
			$update_stmt->bind_param('ssii', $datecompleted, $completedby, $is_off ,$jobid, $taskid); 
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

	//sing off
	if ($_POST['action'] == "singOff"){
		if ($_SESSION['is_JobApprove'] == 1){

			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];
			$datecompleted = date("Y-m-d H:i:s");
			$completedby = $_SESSION['user_id'];
			$is_off = 1;

			$update_stmt = $mysqli->prepare("UPDATE tblJobTaskDraft SET DateCompleted = ?, CompletedBy = ?, is_off = ? WHERE JobID = ? AND TaskID = ?");
			$update_stmt->bind_param('ssiii', $datecompleted, $completedby, $is_off, $jobid, $taskid); 
			$update_stmt->execute();
					
			if ($update_stmt->affected_rows != -1){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The job was sing off successfully.</div>";
			}
			else{
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be sing off.</div>";
			}
			echo json_encode($data);
		}
	}

	//sing off All
	if ($_POST['action'] == "singOffAll"){
		if ($_SESSION['is_JobApprove'] == 1){

			$jobid = $_POST['jobid'];
			$datecompleted = date("Y-m-d H:i:s");
			$completedby = $_SESSION['user_id'];
			$is_off = 1;

			$update_stmt = $mysqli->prepare("UPDATE tblJobTaskDraft SET DateCompleted = ?, CompletedBy = ?, is_off = ? WHERE JobID = ?");
			$update_stmt->bind_param('ssii', $datecompleted, $completedby, $is_off, $jobid); 
			$update_stmt->execute();
					
			if ($update_stmt->affected_rows != -1){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The job was sing off successfully.</div>";
			}
			else{
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be sing off.</div>";
			}
			echo json_encode($data);
		}
	}

	// add new task room
	if ($_POST['action'] == "taskInsert"){

		$taskName = $_POST['task_name'];
		$Weight = 1;

		$insert_stmt = $mysqli->prepare("INSERT INTO tbltask (TaskName,Weight) VALUES (?,?)");
		$insert_stmt->bind_param('si', $taskName,$Weight); 
		$insert_stmt->execute();
				
		if ($insert_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The task room successfully.</div>";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The task room not add.</div>";
		}
		echo json_encode($data);
	}

	// add new room
	if ($_POST['action'] == "createRoom"){

		$taskId = $_POST['roomId'];
		$jobid = $_POST['jobid'];
		$DateStarted = date("Y-m-d H:i:s");
		$userId = $_SESSION['user_id'];

		$insert_stmt = $mysqli->prepare("INSERT INTO tbljobtaskdraft (JobID,TaskID,DateStarted,StartedBy) VALUES (?,?,?,?)");
		
		$insert_stmt->bind_param('iisi', $jobid,$taskId,$DateStarted,$userId); 
		$insert_stmt->execute();
				
		if ($insert_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The room add successfully.</div>";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The room not add.</div>";
		}
		echo json_encode($data);
	}


	// save checklist and missing items
	if ($_POST['action'] == "savechecklist"){
		if ($_SESSION['is_draftsman'] <> 0){

			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];
			
			$notes = trim($_POST['inputNotes']);
			$missingitems = trim($_POST['inputMissingItems']);
			$missingitemscomplete = isset($_POST['inputMissingItemsComplete']) ? $_POST['inputMissingItemsComplete'] : 0;

			$update_stmt = $mysqli->prepare("UPDATE tblJobTaskDraft SET Notes = ?, MissingItems = ?, MissingItemsComplete = ? WHERE JobID = ? AND TaskID = ?");
			$update_stmt->bind_param('ssiii', $notes, $missingitems, $missingitemscomplete, $jobid, $taskid); 
			$update_stmt->execute();
					
			if ($update_stmt->affected_rows != -1){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The checklist was saved successfully.</div>";
			}
			else{
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The checklist could not be saved</div>";
			}
				
			echo json_encode($data);
		}
	}

	// save checklist and missing items
	if ($_POST['action'] == "savematerials"){
		if ($_SESSION['is_draftsman'] <> 0){

			//update existing materials
			if (isset($_POST['inputJobMaterialID'])){
				foreach ($_POST['inputJobMaterialID'] as $k => $v) {
					
					$jobmaterialid = $_POST['inputJobMaterialID'][$k];
					$boarddescription = $_POST['inputBoardDescription'][$k];
					$boardtype = $_POST['inputBoardType'][$k];
					$boardquantity = $_POST['inputBoardQuantity'][$k];

					$delete = $_POST['inputMaterialDelete'][$k];

					if ($delete != "1"){
						$line_update_stmt = $mysqli->prepare("UPDATE tblJobMaterial SET BoardDescription = ?, BoardType = ?, BoardQuantity = ? WHERE JobMaterialID = ?"); 
						$line_update_stmt->bind_param('ssii', $boarddescription, $boardtype, $boardquantity, $jobmaterialid); 
				
						$line_update_stmt->execute();
					}
					else
					{
						//delete removed items
						$stmt = $mysqli->prepare("DELETE FROM tblJobMaterial WHERE JobMaterialID = ? LIMIT 1");
						$stmt->bind_param("i",$jobmaterialid);     
						$stmt->execute();
					}
				}
			}

			//add materials
			if (isset($_POST['inputBoardDescriptionAdd'])){
				foreach ($_POST['inputBoardDescriptionAdd'] as $k => $v) {
					$jobid = $_POST['jobid'];
					$boarddescription = $_POST['inputBoardDescriptionAdd'][$k];
					$boardtype = $_POST['inputBoardTypeAdd'][$k];
					$boardquantity = $_POST['inputBoardQuantityAdd'][$k];
					$cancel = $_POST['inputMaterialCancelAdd'][$k];
					
					if ($cancel != "1"){
						$line_insert_stmt = $mysqli->prepare("INSERT INTO tblJobMaterial (JobID, BoardDescription, BoardType, BoardQuantity) Values (?, ?, ?, ?)"); 
						$line_insert_stmt->bind_param('issi', $jobid, $boarddescription, $boardtype, $boardquantity); 
				
						$line_insert_stmt->execute();
					}
				}
			}
			$data['msg'] = "<div class='alert alert-success' role='alert'>The materials were saved successfully.</div>";
			echo json_encode($data);
		}
	}

}
?>