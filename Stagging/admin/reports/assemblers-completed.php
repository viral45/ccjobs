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
	$where .= " AND tblJobTask.DateCompleted BETWEEN '" . date("Y-m-d", strtotime($dates[0])) . "' AND '" . date("Y-m-d", strtotime($dates[1] . '+1 day')) . "'";
}

if (!empty($_REQUEST['searchStartedBy'])){	
	$where .= " AND tblJobTask.StartedBy = '" . $_REQUEST['searchStartedBy'] . "'";
}

if (!empty($_REQUEST['sortorder'])){	
    $sortorder = $_REQUEST['sortorder'];
}
?>

<div id="print-content">
    <div>
    
    <h1>
        <span id="pageTitle">Assembler Jobs (completed)</span>
        <button type="button" id="print-btn" class="btn btn-default pull-right no-print"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Print</button>
        <img class="report-logo pull-right" src="img/report_logo.png" alt="Challenge Cabinets Logo" />
    </h1>
    </div>
    
    <div class="table">
    
        <?php
			$query = "SELECT Count(tblJob.JobID) FROM tblJob INNER JOIN tblJobTask ON tblJob.JobID = tblJobTask.JobID $where AND tblJobTask.DateCompleted IS NOT NULL ORDER BY tblJob.JobID";
            
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
                        <th>Checked</th>
                        <th>Completed</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php 
                    
                	$query = "SELECT tblJob.JobID, tblJob.JobAddress, tblTask.TaskName, tblJobTask.DateStarted, tblJobTask.DateChecked, tblJobTask.DateCompleted, tblUser.FullName FROM ((tblJob INNER JOIN tblJobTask ON tblJob.JobID = tblJobTask.JobID) INNER JOIN tblTask ON tblJobTask.TaskID = tblTask.TaskID) INNER JOIN tblUser ON tblJobTask.StartedBy = tblUser.UserID $where AND tblJobTask.DateCompleted IS NOT NULL ORDER BY $sortorder";
					$result = $mysqli->query($query);
                    
                    $totaljobs = 0;
                    while($row = $result->fetch_array()){
                    	
                    	$totaljobs++;
                    ?>
                        <tr>
                            <td ><?php echo "<a href='../job.php?jobid=" . $row['JobID'] . "#assembler' target='_blank'>" . $row['JobID'] . "</a>" ?></td>
	                        <td ><?php echo $row['JobAddress'] ?></td>
                            <td><?php echo $row['TaskName'] ?></td>
                            <td><?php if (!empty($row['DateStarted'])){echo date("d-m-Y", strtotime($row['DateStarted'])) . " by " . $row['FullName']; } ?></td>
                            <td><?php if (!empty($row['DateChecked'])){echo date("d-m-Y", strtotime($row['DateChecked'])); } ?></td>
                            <td><?php if (!empty($row['DateCompleted'])){echo date("d-m-Y", strtotime($row['DateCompleted'])); } ?></td>	    
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