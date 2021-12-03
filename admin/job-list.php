<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

if (isset($_REQUEST["page"])) { $page  = $_REQUEST["page"]; } else { $page=1; };  

//search variables
$where = " WHERE JobID IS NOT NULL AND Deleted = 0";

if (!empty($_REQUEST['searchJobNo'])){	
	$where .= " AND JobID LIKE '%" . $mysqli->real_escape_string($_REQUEST['searchJobNo']) . "%'";
}

if (!empty($_REQUEST['searchAddress'])){	
	$where .= " AND JobAddress LIKE '%" . $mysqli->real_escape_string($_REQUEST['searchAddress']) . "%'";
}

?>

<div class="table-responsive">

	<?php
		$query = "SELECT COUNT(JobID) FROM tblJob $where";

		$result = $mysqli->query($query);
		$row = $result->fetch_array(); 
		$total_records = $row[0];  
		$total_pages = ceil($total_records / $recordsperpage); 

		if ($page > $total_pages)
			$page = $total_pages;
			
		$start_from = ($page-1) * $recordsperpage;
		
		if ($row[0] > 0 ){
	?> 
    
        <table class="table table-striped">
            <thead>
                <tr>
                    <th nowrap>Job No.</th>
                    <th>Address</th>
                    <th>Date Entered</th>
                    <th>Measure Date</th>
                    <th style="width:40px;"></th>
                </tr>
            </thead>
            <tbody>
                
                <?php 
                   
                $query = "SELECT JobID, ProjectID, JobAddress, DateEntered, DateMeasure FROM tblJob $where ORDER BY ProjectID LIMIT $start_from, $recordsperpage";

                $result = $mysqli->query($query);
                $getProjectName = '';
                while($row = $result->fetch_array()){

                    $ProjectName = '';

                    if($row['ProjectID'] != '')
                    {
                        
                        if ($stmt = $mysqli->prepare("SELECT ProjectName FROM tblproject WHERE ProjectID = ? LIMIT 1")) { 
                            $stmt->bind_param('i', $row['ProjectID']);
                            $stmt->execute();   
                            $stmt->store_result();
                            $stmt->bind_result($ProjectName);
                            $stmt->fetch();
                        }
                    }

                    if($getProjectName != $ProjectName)
                    {
                        $getProjectName = $ProjectName;
                        ?>

                        <tr>
                            <td colspan="5" class="text-center "><h5><?php echo $getProjectName; ?></h5></td>
                        </tr>
                    <?php
                    }
                ?>
                    <tr>
                        <td ><?php echo $row['JobID'] ?></td>
                        <td ><?php echo $row['JobAddress'] ?></td>                       
                        <td><?php echo date("d-m-Y", strtotime($row['DateEntered'])) ?></td>
                        <td><?php echo (!empty($row['DateMeasure']) ? date("d-m-Y", strtotime($row['DateMeasure']))  : ""); ?></td>

                        <td nowrap>   
                            <button type="button" value="<?php echo $row['JobID']; ?>" class="btn btn-primary btn-xs edit-btn" data-toggle="tooltip" data-placement="top" title="Edit"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                            <button type="button" value="<?php echo $row['JobID']; ?>" class="btn btn-info btn-xs history-btn" data-toggle="tooltip" data-placement="top" title="History"><span class="glyphicon glyphicon-time" aria-hidden="true"></span></button>                        
                        </td>
                    </tr>
                    
                <?php } ?>
            </tbody>   
        </table>
        
    <?php } else { echo "No records were found."; } ?>
    
</div>

<?php 
if ($total_pages > 1)
{
?>
<nav class="pagination-nav">
   
  <ul class="pagination">
    <?php
      for ($i=1; $i<=$total_pages; $i++) {  
            $active = ($i == $page) ? "class='active'" : "";
            echo "<li><a href='jobs.php?page=".$i."'>".$i."</a></li>";
        };
    ?> 
  </ul>
</nav>

<script>
    $('.pagination').pagination({
        items: <?php echo $total_records;?>,
        itemsOnPage: <?php echo $recordsperpage;?>,
        cssStyle: 'light-theme',
        currentPage : <?php echo $page;?>,
        hrefTextPrefix : 'jobs.php?page='
    });
</script>

<?php
}
	 
?>	