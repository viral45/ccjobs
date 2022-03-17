<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
	die();
}

include("config.php"); 
$page = "welcome";
$recordsperpageLink = 8;

if (isset($_REQUEST["userpage"])) { $page  = $_REQUEST["userpage"]; } else { $page=1; }; 


	$query = "SELECT * FROM tblUser WHERE Active = 1";
	//echo $query;die; 
	$result = $mysqli->query($query);
	$row = $result->fetch_array(); 
	$total_records = $row[0];  
	$total_pages = ceil($total_records / $recordsperpageLink); 

	if ($page > $total_pages)
		$page = $total_pages;
		
	$start_from = ($page-1) * $recordsperpageLink;


$data = "SELECT * FROM tblUser WHERE Active = 1 LIMIT $start_from,$recordsperpageLink";
$result = $mysqli->query($data);

			$count = 0;
			while($row = $result->fetch_array())
			{
		?>	
				

					<div class="tbl_jms tbl_jms_users">
						<p class="job_user">User# |  <?php echo $row['UserID']; ?>  <?php echo $row['FullName']; ?></p>
						<div class="job_action_btn">
							<a href="../my-jobs.php" type="button" class="btn btn-success">Jobs</a>
							<a href="../drawer-schedule.php" type="button" class="btn btn-success">Schedule</a>
							<a href="../installer-missing.php" type="button" class="btn btn-success">Missing</a>
							<a href="#" type="button" class="btn btn-success">Edit</a>
						</div>
					</div>
			
		<?php 
			$count++;
			}
		?>


<?php 
if ($total_pages > 1)
{
?>
<nav class="pagination-nav">
   
  <ul class="pagination p-User">
    <?php
      for ($i=1; $i<=$total_pages; $i++) {  
            $active = ($i == $page) ? "class='active'" : "";
            echo "<li><a class='user-page-link' href='index-user-list.php?userpage=".$i."'>".$i."</a></li>";
        };
    ?> 
  </ul>
</nav>

<script>
    $('.pagination.p-User').pagination({
        items: <?php echo $total_records;?>,
        itemsOnPage: <?php echo $recordsperpageLink;?>,
        cssStyle: 'light-theme',
        currentPage : <?php echo $page;?>,
        //hrefTextPrefix : 'index-user-list.php?userpage='
        onPageClick:function(pageNumber) {
            showUsers(pageNumber);
        }
    });

    

</script>

<?php
}
	 
?>	