<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

?>

<div id="print-content">
    <div>
        <h1>
            <span id="pageTitle">Jobs (missing items) - Assemblers</span>
            <button type="button" id="print-btn" class="btn btn-default pull-right no-print"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Print</button>
            <img class="report-logo pull-right" src="img/report_logo.png" alt="Challenge Cabinets Logo" />
        </h1>
    </div>
    
    <div class="table">
    
        <?php
			$query = "SELECT Count(tblJob.JobID) FROM tblJob INNER JOIN tblJobTask ON tblJob.JobID = tblJobTask.JobID WHERE tblJob.Deleted = 0 AND (tblJobTask.MissingItems IS NOT NULL AND tblJobTask.MissingItems != '') AND tblJobTask.MissingItemsComplete = 0 GROUP BY tblJob.JobID";
            
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
                        <th>Started</th>
                        <th>Missing Items</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php 
                	$query = "SELECT tblJob.JobID, tblJob.JobAddress, tblTask.TaskName, tblJobTask.DateStarted, tblJobTask.StartedBy, tblJobTask.DateChecked, tblUser.FullName, tblJobTask.DateCompleted, tblJobTask.CompletedBy, tblJobTask.MissingItems FROM ((tblJob INNER JOIN tblJobTask ON tblJob.JobID = tblJobTask.JobID) INNER JOIN tblTask ON tblJobTask.TaskID = tblTask.TaskID) INNER JOIN tblUser ON tblJobTask.StartedBy = tblUser.UserID WHERE tblJob.Deleted = 0 AND (tblJobTask.MissingItems IS NOT NULL AND tblJobTask.MissingItems != '') AND tblJobTask.MissingItemsComplete = 0 ORDER BY tblJob.JobID";
					$result = $mysqli->query($query);
                    
                    $totaljobs = 0;
                    while($row = $result->fetch_array()){
                    	
                    	$totaljobs++;
                    ?>
                        <tr>
                            <td><?php echo $row['JobID'] ?></td>
	                        <td><?php echo $row['JobAddress'] ?></td>
                            <td><?php echo $row['TaskName'] ?></td>
                            <td><?php if (!empty($row['DateStarted'])){echo date("d-m-Y", strtotime($row['DateStarted'])) . " by " . $row['FullName']; } ?></td>
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

    <div>
        <br>
        <h1>
            <span id="pageTitle">Jobs (missing items) - Installers</span>
        </h1>
    </div>
    
    <div class="table">
    
        <?php
			$query = "SELECT Count(tblJob.JobID) FROM tblJob INNER JOIN tblJobTaskInstall ON tblJob.JobID = tblJobTaskInstall.JobID WHERE tblJob.Deleted = 0 AND (tblJobTaskInstall.MissingItems IS NOT NULL AND tblJobTaskInstall.MissingItems != '') AND tblJobTaskInstall.MissingItemsComplete = 0 GROUP BY tblJob.JobID";
            
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
                        <th>Checked</th>
                        <th>Missing Items</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php 
                	$query = "SELECT tblJob.JobID, tblJob.JobAddress, tblTask.TaskName, tblJobTaskInstall.DateChecked, tblJobTaskInstall.CheckedBy, tblUser.FullName, tblJobTaskInstall.DateCompleted, tblJobTaskInstall.CompletedBy, tblJobTaskInstall.MissingItems FROM ((tblJob INNER JOIN tblJobTaskInstall ON tblJob.JobID = tblJobTaskInstall.JobID) INNER JOIN tblTask ON tblJobTaskInstall.TaskID = tblTask.TaskID) INNER JOIN tblUser ON tblJobTaskInstall.CheckedBy = tblUser.UserID WHERE tblJob.Deleted = 0 AND (tblJobTaskInstall.MissingItems IS NOT NULL AND tblJobTaskInstall.MissingItems != '') AND tblJobTaskInstall.MissingItemsComplete = 0 ORDER BY tblJob.JobID";
					$result = $mysqli->query($query);
                    
                    $totaljobs = 0;
                    while($row = $result->fetch_array()){
                    	
                    	$totaljobs++;
                    ?>
                        <tr>
                            <td><?php echo $row['JobID'] ?></td>
	                        <td><?php echo $row['JobAddress'] ?></td>
                            <td><?php echo $row['TaskName'] ?></td>
                            <td><?php if (!empty($row['DateChecked'])){echo date("d-m-Y", strtotime($row['DateChecked'])) . " by " . $row['FullName']; } ?></td>
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