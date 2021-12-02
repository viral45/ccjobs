<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

if (isset($_POST['action']))
{
	if ($_POST['action'] == "edit"){
		
		$userid = isset($_POST['userid']) ? $_POST['userid'] : die('ERROR: User ID not found.');

		if ($stmt = $mysqli->prepare("SELECT UserID, FullName, Email FROM tblAdminUser WHERE UserID = ? LIMIT 1")) { 
			$stmt->bind_param('s', $userid);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($formuserid, $formfullname, $formemail);
			$stmt->fetch();
		}
	}
}
?>

<form id='user-form' action='#' method='post'>
    <input type="hidden" id="action" name="action" value="<?php if (isset($_POST['action'])) { echo $_POST['action']; } ?>">
    <input type="hidden" id="userid" name="userid" value="<?php if (isset($_POST['userid'])) { echo $_POST['userid']; } ?>">
    
    <div class="form-group">
        <label for="inputFullName">Name</label>
        <input type="text" class="form-control" id="inputFullName" name="inputFullName" placeholder="Full Name" required value="<?php if (isset($formfullname)) { echo htmlspecialchars($formfullname, ENT_QUOTES); } ?>">
    </div>
    <div class="form-group">
        <label for="inputUsername">Username</label>
        <input type="text" class="form-control" id="inputUsername" name="inputUsername" placeholder="Username" required value="<?php if (isset($formuserid)) { echo htmlspecialchars($formuserid, ENT_QUOTES); } ?>">
    </div>
    <div class="form-group">
        <label for="inputPassword">Password</label>
        <input type="password" class="form-control" id="inputPassword" name="inputPassword" placeholder="Password" <?php if ($_POST['action'] == "add"){ echo "required"; } ?>>
    </div>
    <div class="form-group">
        <label for="inputEmail">Email address</label>
        <input type="email" class="form-control" id="inputEmail" name="inputEmail" placeholder="Email" required value="<?php if (isset($formemail)) { echo htmlspecialchars($formemail, ENT_QUOTES); } ?>">
    </div>
        
    <hr />
    
    <button type="submit" id="save-btn" class="btn btn-primary">Save</button>
	<button type="button" id="return-btn" class="btn btn-warning">Return</button>
</form>