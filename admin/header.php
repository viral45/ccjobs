<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../img/favicon.ico">

    <title>Challenge Cabinets Admin</title>

    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=1.4" rel="stylesheet">
	<link rel="stylesheet" href="css/bootstrap-select.min.css">
	<link rel="stylesheet" href="css/daterangepicker-bs3.css">
    <link href="../font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/jquery-ui.min.css">
    <link type="text/css" rel="stylesheet" href="css/simplePagination.css"/>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <script src="../js/jquery-1.11.3.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
  	<script src="js/ie10-viewport-bug-workaround.js"></script>
    <script src="js/jquery.confirm.min.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/additional-methods.min.js"></script>
    <script src="js/jquery.mask.min.js"></script>
	<script src="js/bootstrap-filestyle.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/moment.min.js"></script>
	<script src="js/daterangepicker.js"></script>
  <script src="js/jQuery.print.js"></script>
  <script src="js/jquery-ui.min.js"></script>
  <script src="js/jquery.simplePagination.js"></script>
  
  </head>

  <body>
	
    <!-- Fixed navbar -->
    <nav class="navbar navbar-fixed-top navbar-inverse">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><img src="../img/logo_small.png" alt="Challenge Cabinets" /></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
                 
                        
            <li <?php echo ($page == "welcome") ? "class='active'" : ""; ?>><a href="index.php">Home</a></li>

            <li <?php echo ($page == "jobs") ? "class='active'" : ""; ?>><a href="jobs.php">Jobs</a></li>

            <li class="dropdown <?php echo ($page == "schedule") ? "active" : ""; ?>" >
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Schedule<span class="caret"></span></a>
              <ul class="dropdown-menu dropdown-menu-left">
                <li><a href="schedule.php">Installers</a></li>
                <li><a href="delivery.php">Delivery</a></li>
                <li><a href="drawers.php">Draftsman</a></li>
                <li><a href="stone.php">Stone</a></li>
                <li><a href="assemblers.php">Assemblers</a></li>
              </ul>
            </li>

            <li class="dropdown <?php echo ($page == "reports") ? "active" : ""; ?>" >
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Reports<span class="caret"></span></a>
              <ul class="dropdown-menu dropdown-menu-left">
                <li><a href="reports-draftsmen.php">Draftsmen</a></li>
                <li><a href="reports-cnc.php">CNC</a></li>
                <li><a href="reports-edging.php">Edging</a></li>
                <li><a href="reports-assemblers.php">Assemblers</a></li>
                <li><a href="reports-installers.php">Installers</a></li>
              </ul>
            </li>

            <li <?php echo ($page == "users") ? "class='active'" : ""; ?>><a href="users.php">Users</a></li>
            
            <li <?php echo ($page == "adminusers") ? "class='active'" : ""; ?>><a href="admin_users.php">Admin Users</a></li>

            <li><a href="logout.php">Log Out</a></li>

          </ul>

        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">    
