<?php

	include("config.php");
	include("functions.php");
	include("password.php"); 
	
	sec_session_start();
	
	if(isset($_POST['inputUsername'], $_POST['inputPassword'])) { 
	   $username = $_POST['inputUsername'];
	   $password = $_POST['inputPassword'];
	   if(login($username, $password, $mysqli) == true) {
		  // Login success
		  header('Location: index.php');
		  
	   } else {
		  // Login failed
		  $message = "Incorrect Username or Password!";
	   }
	} 

	function login($username, $password, $mysqli) {

	   if ($stmt = $mysqli->prepare("SELECT UserID, Password FROM tblAdminUser WHERE UserID = ? LIMIT 1")) { 
		  $stmt->bind_param('s', $username);
		  $stmt->execute();
		  $stmt->store_result();
		  $stmt->bind_result($user_id, $db_password);
		  $stmt->fetch();
	 	
		  if($stmt->num_rows == 1) { // If the user exists
			 if (password_verify($password, $db_password)){// Check if the password in the database matches the password the user submitted. 
					
					// Password is correct!
					$_SESSION['logged_in'] = true; 
				   	$_SESSION['admin_id'] = $user_id; 
					
				   	// Login successful.
				   	return true;    
			 } else {
				// Password is not correct
				return false;
			 }
		  } else {
			 // No user exists. 
			 return false;
		  }
	   }
	}
	
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/favicon.ico">

    <title>Challenge Cabinets Admin Login</title>

    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="css/signin.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">

      <form class="form-signin" method="post" action="login.php">
        <img src="../img/logo_large.png" class="img-responsive" alt="Challenge Cabinets">
        <br />
        <?php if (isset($message)){?><div class="alert alert-danger" role="alert"><?php echo $message ?></div><?php } ?>
        
        <label for="inputUsername" class="sr-only">Username</label>
        <input type="text" id="inputUsername" name="inputUsername" class="form-control" placeholder="Username" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Password" required>

        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      </form>

    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>