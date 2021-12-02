<?php
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 
include("password.php"); 

if (isset($_POST['action'])){	
	
	//add a user
	if ($_POST['action'] == "add"){
		
		$name = $_POST['inputFullName'];
		$username = $_POST['inputUsername']; 
		$email = $_POST['inputEmail']; 
		$password = $_POST['inputPassword'];

		if ($password != "")
			$passwordHash = password_hash($password, PASSWORD_BCRYPT);
			
		$insert_stmt = $mysqli->prepare("INSERT INTO tblAdminUser (FullName, UserID, Email, Password) VALUES (?, ?, ?, ?)");
		$insert_stmt->bind_param('ssss', $name, $username, $email, $passwordHash); 
		$insert_stmt->execute();
				
		if ($insert_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The user was added successfully.</div>";
			$data['last_insert_id'] = $username;
			$data['action'] = "edit";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The user could not be added</div>";
			$data['action'] = "add";
		}
			
		echo json_encode($data);
	}
	
	//edit an existing user
	if ($_POST['action'] == "edit"){
		
		$userid = $_POST['userid'];
		$name = $_POST['inputFullName'];
		$username = $_POST['inputUsername']; 
		$email = $_POST['inputEmail']; 
		$password = $_POST['inputPassword'];
		
		if ($password != "")
			$passwordHash = password_hash($password, PASSWORD_BCRYPT);
			
		if ($password == ""){
			$update_stmt = $mysqli->prepare("UPDATE tblAdminUser SET FullName = ?, UserID = ?, Email = ? WHERE UserID = ?"); 
			$update_stmt->bind_param('ssss', $name, $username, $email, $userid); 
		}
		else{
			$update_stmt = $mysqli->prepare("UPDATE tblAdminUser SET FullName = ?, UserID = ?, Email = ?, Password = ? WHERE UserID = ?"); 
			$update_stmt->bind_param('sssss', $name, $username, $email, $passwordHash, $userid); 
		}
			
		$update_stmt->execute();
		
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The user was updated successfully.</div>";
			$data['last_insert_id'] = $username;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The user could not be updated.</div>";
			$data['last_insert_id'] = $userid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}
	
	//delete user
	if ($_POST['action'] == "delete"){
		if (isset($_POST['deleteid'])){
			$deleteid = $_POST['deleteid'];
			$stmt = $mysqli->prepare("DELETE FROM tblAdminUser WHERE UserID = ? LIMIT 1");
			$stmt->bind_param("s",$deleteid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected user was deleted successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected user could not be deleted</div>";
				
			$stmt->close();
		}
	}
}
?>