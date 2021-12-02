<?php
chdir(__DIR__);

include("config.php"); 

require('classes/class.phpmailer.php');
require('classes/class.smtp.php');

$email_from = "no-reply@challengecabinetsjobs.com.au";
$headers = 'From: '.$email_from."\r\n". 'Reply-To: '.$email_from."\r\n" . 'X-Mailer: PHP/' . phpversion();

//get list of foreman emails
$query = "SELECT Email FROM tblUser WHERE IsForeman <> 0 AND SendAlerts <> 0 AND Active <> 0";
$result = $mysqli->query($query);
while ($row = $result->fetch_array())
	$emails[] = $row['Email'];

if (!empty($emails)){

	$jobquery = "SELECT tblJob.JobID, tblJob.JobAddress, tblTask.TaskName, tblUser.FullName FROM ((tblJob INNER JOIN tblJobTask ON tblJob.JobID = tblJobTask.JobID) INNER JOIN tblTask ON tblJobTask.TaskID = tblTask.TaskID) INNER JOIN tblUser ON tblJobTask.StartedBy = tblUser.UserID WHERE tblJob.Deleted = 0 AND tblJobTask.ReadyForCheck <> 0 AND tblJobTask.DateChecked IS NULL GROUP BY tblJob.JobID";
	$jobresult = $mysqli->query($jobquery);

	while ($jobrow = $jobresult->fetch_array()){
		
		$email_subject = "Job #" . $jobrow['JobID'] . " - " . $jobrow['JobAddress'] . " (" . $jobrow['TaskName'] . " - Assembler: " . $jobrow['FullName'] . ")  is ready to be checked";
		$email_message = "Job #" . $jobrow['JobID'] . " - " . $jobrow['JobAddress'] . " (" . $jobrow['TaskName'] . " - Assembler: " . $jobrow['FullName'] . ")  is ready to be checked<br><br><a href='http://www.challengecabinetsjobs.com.au'>www.challengecabinetsjobs.com.au</a>";

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
}






        	

	

?>