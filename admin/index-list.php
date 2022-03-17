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

if (isset($_REQUEST["jobpage"])) { $page  = $_REQUEST["jobpage"]; } else { $page=1; }; 


	$query = "SELECT COUNT(JobID) FROM tblJobTask WHERE tblJobTask.MissingItemsComplete = 0 AND tblJobTask.MissingItems != '' AND tblJobTask.MissingItems IS NOT NULL";
	//echo $query;die; 
	$result = $mysqli->query($query);
	$row = $result->fetch_array(); 
	$total_records = $row[0];  
	$total_pages = ceil($total_records / $recordsperpageLink); 

	if ($page > $total_pages)
		$page = $total_pages;
		
	$start_from = ($page-1) * $recordsperpageLink;


$data = "SELECT * FROM tblJobTask  WHERE tblJobTask.MissingItemsComplete = 0 AND tblJobTask.MissingItems != '' AND tblJobTask.MissingItems IS NOT NULL LIMIT $start_from,$recordsperpageLink";
$result = $mysqli->query($data);

			$count = 0;
			while($row = $result->fetch_array())
			{
		?>	
					<div class="tbl_jms">
						<p class="tbl_jms_id"><a href="../job.php?jobid=<?php echo $row['JobID']; ?>#draftsman">Job# |<?php echo $row['JobID']; ?></a></p>
						<p class="tbl_jms_missingitems"><?php echo $row['MissingItems']; ?></p>
					</div>				
			
		<?php 
			
			}
		?>


<?php 
if ($total_pages > 1)
{
?>
<nav class="pagination-nav" id="p_job">
   
  <ul class="pagination p-jobs" >
    <?php
      for ($i=1; $i<=$total_pages; $i++) {  
            $active = ($i == $page) ? "class='active'" : "";
            echo "<li id='jobs'><a href='index-list.php?jobpage=".$i."' onclick=return false;>".$i."</a></li>";
        };
    ?> 
  </ul>
</nav>

<script>

    $('.pagination.p-jobs').pagination({
        items: <?php echo $total_records;?>,
        itemsOnPage: <?php echo $recordsperpageLink;?>,
        cssStyle: 'light-theme',
        currentPage : <?php echo $page;?>,
        //hrefTextPrefix : 'index-list.php?jobpage=',
        onPageClick:function(pageNumber) {
            showJobs(pageNumber);
        }
    });

    

</script>

<?php
}
	 
?>	