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
		if ($_SESSION['is_maint'] <> 0){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];
			$datecompleted = date("Y-m-d H:i:s");
			$completedby = $_SESSION['user_id'];

			$update_stmt = $mysqli->prepare("UPDATE tblJobTaskMaint SET DateCompleted = ?, CompletedBy = ? WHERE JobID = ? AND TaskID = ?");
			$update_stmt->bind_param('ssii', $datecompleted, $completedby, $jobid, $taskid); 
			$update_stmt->execute();
					
			if ($update_stmt->affected_rows != -1){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The job was completed successfully.</div>";

				//send email to foreman
				require('classes/class.phpmailer.php');
				require("classes/class.smtp.php");

				$email_from = "no-reply@challengecabinetsjobs.com.au";

				$jobquery = "SELECT tblJob.JobID, tblJob.JobAddress, tblTask.TaskName, tblUser.FullName FROM ((tblJob INNER JOIN tblJobTaskInstall ON tblJob.JobID = tblJobTaskInstall.JobID) INNER JOIN tblTask ON tblJobTaskInstall.TaskID = tblTask.TaskID) INNER JOIN tblUser ON tblJobTaskInstall.CompletedBy = tblUser.UserID WHERE tblJobTaskInstall.JobID = $jobid AND tblJobTaskInstall.TaskID = $taskid";
				$jobresult = $mysqli->query($jobquery);
				$jobrow = $jobresult->fetch_array();

				$email_subject = "Job #" . $jobid . " - " . $jobrow['JobAddress'] . " (" . $jobrow['TaskName'] . " - Installer: " . $jobrow['FullName'] . ")" . " maintenance has been completed";
				$email_message = "Job #" . $jobid . " - " . $jobrow['JobAddress'] . " (" . $jobrow['TaskName'] . " - Installer: " . $jobrow['FullName'] . ")" . " maintenance has been completed<br><br><a href='http://www.challengecabinetsjobs.com.au'>www.challengecabinetsjobs.com.au</a>";
			
				$headers = 'From: '.$email_from."\r\n". 'Reply-To: '.$email_from."\r\n" . 'X-Mailer: PHP/' . phpversion();
				
				$mail = new PHPMailer(true);
				//$mail->IsSMTP();

				//$mail->Host = "mail.bigpond.com.au";
				
				$mail->SetFrom($email_from);

				$query = "SELECT Email FROM tblUser WHERE IsForeman <> 0 AND SendAlerts <> 0 AND Active <> 0";
				$result = $mysqli->query($query);

				if ($result->num_rows > 0){
					while ($row = $result->fetch_array())
						$mail->AddAddress($row['Email']);

					$mail->Subject = $email_subject;
					$mail->Body = $email_message;
					$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
					
					$mail->Send();
				}
			}
			else{
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be completed</div>";
			}
				
			echo json_encode($data);
		}
	}

	// save checklist and missing items
	if ($_POST['action'] == "savechecklist"){
		if ($_SESSION['is_maint'] <> 0){

			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];
			
			$notes = trim($_POST['inputNotes']);
			$missingitems = trim($_POST['inputMissingItems']);
			$missingitemscomplete = isset($_POST['inputMissingItemsComplete']) ? $_POST['inputMissingItemsComplete'] : 0;
			
			//$datechecked = date("Y-m-d H:i:s");
			//$checkedby = $_SESSION['user_id'];
			
			$update_stmt = $mysqli->prepare("UPDATE tblJobTaskMaint SET Notes = ?, MissingItems = ?, MissingItemsComplete = ? WHERE JobID = ? AND TaskID = ?");
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

}
?>