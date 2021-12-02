<?php
chdir(__DIR__);

include("config.php"); 

require('classes/class.phpmailer.php');
require('classes/class.smtp.php');

$enabled = 0;

if ($enabled == 1){

	$email_from = "no-reply@challengecabinetsjobs.com.au";
	$headers = 'From: '.$email_from."\r\n". 'Reply-To: '.$email_from."\r\n" . 'X-Mailer: PHP/' . phpversion();
	
	//get list of foreman emails
	$query = "SELECT Email FROM tblUser WHERE IsForeman <> 0 AND SendAlerts <> 0 AND Active <> 0";
	$result = $mysqli->query($query);
	while ($row = $result->fetch_array())
		$emails[] = $row['Email'];
	
	if (!empty($emails)){
		//assemblers
		$jobquery = "SELECT tblJob.JobID, tblJob.JobAddress FROM tblJob INNER JOIN tblJobTask ON tblJob.JobID = tblJobTask.JobID WHERE tblJob.Deleted = 0 AND tblJobTask.MissingItems IS NOT NULL AND tblJobTask.MissingItems != '' AND tblJobTask.MissingItemsComplete = 0 GROUP BY tblJob.JobID";
		$jobresult = $mysqli->query($jobquery);
	
		while ($row = $jobresult->fetch_array()){
			
			$email_subject = "Job #" . $row['JobID'] . " - " . $row['JobAddress'] . " has missing items";
			$email_message = "Job #" . $row['JobID'] . " - " . $row['JobAddress'] . " has missing items<br><br><a href='http://www.challengecabinetsjobs.com.au'>www.challengecabinetsjobs.com.au</a>";
	
			$mail = new PHPMailer(true);
			//$mail->IsSMTP();
	
			//$mail->Host = "mail.bigpond.com.au";
	
			$mail->SetFrom($email_from);
	
			foreach ($emails as $email)
				$mail->AddAddress($email);
	
			$mail->Subject = $email_subject;
			$mail->Body = $email_message;
			$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
			
			$mail->Send();
		}
	
		//installers
		$jobquery = "SELECT tblJob.JobID, tblJob.JobAddress FROM tblJob INNER JOIN tblJobTaskInstall ON tblJob.JobID = tblJobTaskInstall.JobID WHERE tblJob.Deleted = 0 AND tblJobTaskInstall.MissingItems IS NOT NULL AND tblJobTaskInstall.MissingItems != '' AND tblJobTaskInstall.MissingItemsComplete = 0 GROUP BY tblJob.JobID";
		$jobresult = $mysqli->query($jobquery);
	
		while ($row = $jobresult->fetch_array()){
			
			$email_subject = "Job #" . $row['JobID'] . " - " . $row['JobAddress'] . " has missing items (install)";
			$email_message = "Job #" . $row['JobID'] . " - " . $row['JobAddress'] . " has missing items (install)<br><br><a href='http://www.challengecabinetsjobs.com.au'>www.challengecabinetsjobs.com.au</a>";
	
			$mail = new PHPMailer(true);
			//$mail->IsSMTP();
	
			//$mail->Host = "mail.bigpond.com.au";
	
			$mail->SetFrom($email_from);
	
			foreach ($emails as $email)
				$mail->AddAddress($email);
	
			$mail->Subject = $email_subject;
			$mail->Body = $email_message;
			$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
			
			$mail->Send();
		}
	
		//draftsmen
		//$jobquery = "SELECT tblJob.JobID, tblJob.JobAddress FROM tblJob INNER JOIN tblJobTaskDraft ON tblJob.JobID = tblJobTaskDraft.JobID WHERE tblJob.Deleted = 0 AND tblJobTaskDraft.MissingItems IS NOT NULL AND tblJobTaskDraft.MissingItems != '' AND tblJobTaskDraft.MissingItemsComplete = 0 GROUP BY tblJob.JobID";
		
		$jobquery = "SELECT tblJob.JobID, tblJob.JobAddress, tblUser.Email FROM (tblJob INNER JOIN tblJobTaskDraft ON tblJob.JobID = tblJobTaskDraft.JobID) INNER JOIN tblUser ON tblJob.MeasureBy = tblUser.UserID WHERE tblJob.Deleted = 0 AND tblJobTaskDraft.MissingItems IS NOT NULL AND tblJobTaskDraft.MissingItems != '' AND tblJobTaskDraft.MissingItemsComplete = 0 GROUP BY tblJob.JobID";
		$jobresult = $mysqli->query($jobquery);
	
		while ($row = $jobresult->fetch_array()){
			
			$email_subject = "Job #" . $row['JobID'] . " - " . $row['JobAddress'] . " has missing items (draftsman)";
			$email_message = "Job #" . $row['JobID'] . " - " . $row['JobAddress'] . " has missing items (draftsman)<br><br><a href='http://www.challengecabinetsjobs.com.au'>www.challengecabinetsjobs.com.au</a>";
	
			$mail = new PHPMailer(true);
			//$mail->IsSMTP();
	
			//$mail->Host = "mail.bigpond.com.au";
	
			$mail->SetFrom($email_from);
	
			foreach ($emails as $email)
				$mail->AddAddress($email);
	
			if (!empty($row['Email']))
				$mail->AddAddress($row['Email']);
	
			$mail->Subject = $email_subject;
			$mail->Body = $email_message;
			$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
			
			$mail->Send();
		}
	}
}

?>