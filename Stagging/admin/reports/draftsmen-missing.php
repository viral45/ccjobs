<?php 
include("../functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:../login.php");
}

include("../config.php"); 

if (!empty($_REQUEST['sortorder'])){	
    $sortorder = $_REQUEST['sortorder'];
}

?>

<div id="print-content">
    <div>
        <h1>
            <span id="pageTitle">Draftsmen Jobs (missing items)</span>
            <button type="button" id="print-btn" class="btn btn-default pull-right no-print"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Print</button>
            <img class="report-logo pull-right" src="img/report_logo.png" alt="Challenge Cabinets Logo" />
        </h1>
    </div>
    
    <div class="table">
    
        <?php
			$query = "SELECT Count(tblJob.JobID) FROM tblJob INNER JOIN tblJobTaskDraft ON tblJob.JobID = tblJobTaskDraft.JobID WHERE tblJob.Deleted = 0 AND (tblJobTaskDraft.MissingItems IS NOT NULL AND tblJobTaskDraft.MissingItems != '') AND tblJobTaskDraft.MissingItemsComplete = 0 GROUP BY tblJob.JobID";
            
            $result = $mysqli->query($query);
            $row = $result->fetch_array(); 
                            
            if ($row[0] > 0 ){
        ?> 
        
            <table class="table table-striped">
                <thead>
                    <tr>
	                    <th nowrap>Job No.</th>
	                    <th>Address</th>
                        <th>Area</th>
                        <th>Measured</th>
                        <th>Drawn</th>
                        <th>Missing Items</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php 
                	$query = "SELECT tblJob.JobID, tblJob.JobAddress, tblTask.TaskName, tblJobTaskDraft.DateStarted, tblJobTaskDraft.DateDrawn, tblJobTaskDraft.DrawnBy, tblUser.FullName, tblJobTaskDraft.DateCompleted, tblJobTaskDraft.CompletedBy, tblJobTaskDraft.MissingItems FROM ((tblJob INNER JOIN tblJobTaskDraft ON tblJob.JobID = tblJobTaskDraft.JobID) INNER JOIN tblTask ON tblJobTaskDraft.TaskID = tblTask.TaskID) INNER JOIN tblUser ON tblJobTaskDraft.DrawnBy = tblUser.UserID WHERE tblJob.Deleted = 0 AND (tblJobTaskDraft.MissingItems IS NOT NULL AND tblJobTaskDraft.MissingItems != '') AND tblJobTaskDraft.MissingItemsComplete = 0 ORDER BY $sortorder";
					$result = $mysqli->query($query);
                    
                    $totaljobs = 0;
                    while($row = $result->fetch_array()){
                    	
                    	$totaljobs++;
                    ?>
                        <tr>
                            <td ><?php echo "<a href='../job.php?jobid=" . $row['JobID'] . "#draftsman' target='_blank'>" . $row['JobID'] . "</a>" ?></td>
	                        <td><?php echo $row['JobAddress'] ?></td>
                            <td><?php echo $row['TaskName'] ?></td>
                            <td><?php if (!empty($row['DateStarted'])){echo date("d-m-Y", strtotime($row['DateStarted'])); } ?></td>
                            <td><?php if (!empty($row['DateDrawn'])){echo date("d-m-Y", strtotime($row['DateDrawn'])) . " by " . $row['FullName']; } ?></td>
                            <td><?php echo nl2br($row['MissingItems']); ?></td>
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