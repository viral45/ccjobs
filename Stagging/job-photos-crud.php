<?php
include("functions.php");

sec_session_start();
if(!isset($_SESSION['user_id'])){
	header("location:index.php");
}

include("config.php"); 

if (isset($_POST['action'])){	
	
	//upload plans
	if ($_POST['action'] == "add"){
		if ($_SESSION['is_installer'] <> 0){
			$jobid = $_POST['jobid'];

			foreach($_FILES as $index => $file)
			{
				$allowed =  array('jpg', 'jpeg');
				$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
				
				if(in_array($ext,$allowed)) {
					
					$filelocation = $file['name'];  
					$uploadfilename = file_newname($uploadpath, $filelocation);
					$uploadfile = $uploadpath . $uploadfilename;
					$isplan = 0;
					$isphoto = 1;
								
					$insert_stmt = $mysqli->prepare("INSERT INTO tblJobUpload (JobID, FilePath, IsPlan, IsPhoto) VALUES (?, ?, ?, ?)");
					$insert_stmt->bind_param('isii', $jobid, $uploadfilename, $isplan, $isphoto); 
					$insert_stmt->execute();
					
					if ($insert_stmt->affected_rows != -1 && move_uploaded_file($file['tmp_name'], $uploadfile)){
						resize_image($uploadfile, 1600);
						create_thumbnail($uploadfile, 300, 300, $uploadpath . "thumbs/" . $uploadfilename);

						$data['msg'] = "<div class='alert alert-success' role='alert'>The photo was added successfully.</div>";
					}
					else{
						$data['msg'] = "<div class='alert alert-danger' role='alert'>The photo could not be added</div>";
					}
				}
				else{
					$data['msg'] = "<div class='alert alert-danger' role='alert'>Only '.jpg' files can be uploaded</div>";
				}			
			}

			echo json_encode($data);
		}
	}


	//delete plan
	if ($_POST['action'] == "delete"){
		if (isset($_POST['deleteid'])){
			if ($_SESSION['is_installer'] <> 0){
				//delete file
				$stmt = $mysqli->prepare("SELECT FilePath FROM tblJobUpload WHERE JobUploadID = ?");
				$stmt->bind_param('i', $_POST['deleteid']);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($filelocation);

				while($stmt->fetch()){
					@unlink($uploadpath . $filelocation);
					@unlink($uploadpath . "thumbs/" . $filelocation);
				}
				
				//delete record
				$deleteid = $_POST['deleteid'];
				$stmt = $mysqli->prepare("DELETE FROM tblJobUpload WHERE JobUploadID = ? LIMIT 1");
				$stmt->bind_param("i",$deleteid);     
				$stmt->execute();
				
				if ($stmt->affected_rows != -1)
					echo "<div class='alert alert-success' role='alert'>The selected photo was deleted successfully</div>";
				else
					echo "<div class='alert alert-danger' role='alert'>The selected photo could not be deleted</div>";
					
				$stmt->close();
			}
		}
	}

}
?>