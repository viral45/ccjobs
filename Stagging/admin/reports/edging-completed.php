<?php 
include("../functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:../login.php");
}

include("../config.php"); 

//search variables
$where = " WHERE tblJob.Deleted = 0";

if (!empty($_REQUEST['searchKeyword'])){	
	$where .= " AND (tblJob.JobID LIKE '%" . $mysqli->real_escape_string($_REQUEST['searchKeyword']) . "%' OR tblJob.JobAddress LIKE '%" . $mysqli->real_escape_string($_REQUEST['searchKeyword']) . "%')";
}

if (!empty($_REQUEST['searchDate'])){	
	$dates = explode(" - ",$_REQUEST['searchDate']);
	$where .= " AND tblJobMaterial.EdgingDateCompleted BETWEEN '" . date("Y-m-d", strtotime($dates[0])) . "' AND '" . date("Y-m-d", strtotime($dates[1] . '+1 day')) . "'";
}

if (!empty($_REQUEST['searchCompletedBy'])){	
	$where .= " AND tblJobMaterial.EdgingCompletedBy = '" . $_REQUEST['searchCompletedBy'] . "'";
}

if (!empty($_REQUEST['sortorder'])){	
    $sortorder = $_REQUEST['sortorder'];
}
?>

<div id="print-content">
    <div>
    
    <h1>
        <span id="pageTitle">Edging Jobs (completed)</span>
        <button type="button" id="print-btn" class="btn btn-default pull-right no-print"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Print</button>
        <img class="report-logo pull-right" src="img/report_logo.png" alt="Challenge Cabinets Logo" />
    </h1>
    </div>
    
    <div class="table">
    
        <?php
			$query = "SELECT Count(tblJob.JobID) FROM tblJob INNER JOIN tblJobMaterial ON tblJob.JobID = tblJobMaterial.JobID $where AND tblJobMaterial.EdgingDateCompleted IS NOT NULL AND tblJobMaterial.BoardQuantity <> 0 ORDER BY tblJob.JobID";
            
            $result = $mysqli->query($query);
            $row = $result->fetch_array(); 
                            
            if ($row[0] > 0 ){
        ?> 
        
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th nowrap>Job No.</th>
                        <th>Address</th>
                        <th>Description / Colour</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Notes</th>
                        <th>Completed</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php 
                    
                	$query = "SELECT tblJob.JobID, tblJob.JobAddress, tblJobMaterial.BoardDescription, tblJobMaterial.BoardType, tblJobMaterial.BoardQuantity, tblJobMaterial.EdgingNotes,  tblJobMaterial.EdgingDateCompleted, tblUser.FullName FROM (tblJob INNER JOIN tblJobMaterial ON tblJob.JobID = tblJobMaterial.JobID) INNER JOIN tblUser ON tblJobMaterial.EdgingCompletedBy = tblUser.UserID $where AND tblJobMaterial.EdgingDateCompleted IS NOT NULL AND tblJobMaterial.BoardQuantity <> 0 ORDER BY $sortorder";
					$result = $mysqli->query($query);
                    
                    $totaljobs = 0;
                    while($row = $result->fetch_array()){
                    	
                    	$totaljobs++;
                    ?>
                        <tr>
                            <td ><?php echo "<a href='../job.php?jobid=" . $row['JobID'] . "#edging' target='_blank'>" . $row['JobID'] . "</a>" ?></td>
                            <td ><?php echo $row['JobAddress'] ?></td>
                            <td><?php echo $row['BoardDescription'] ?></td>
                            <td><?php echo $row['BoardType'] ?></td>
                            <td><?php echo $row['BoardQuantity'] ?></td>
                            <td><?php echo nl2br($row['EdgingNotes']) ?></td>
                            <td><?php if (!empty($row['EdgingDateCompleted'])){echo date("d-m-Y", strtotime($row['EdgingDateCompleted'])) . " by " . $row['FullName']; } ?></td>
	    
                        </tr>
                        
                    <?php } ?>
                    
                    <tr class="warning">
					    <td colspan="8"><strong>TOTAL JOBS: <?php echo $totaljobs; ?></strong></td>
					</tr>

                </tbody>   
            </table>
            
        <?php } else { echo "No records were found."; } ?>
        
    </div>
</div>