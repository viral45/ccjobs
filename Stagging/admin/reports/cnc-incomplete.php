<?php 
include("../functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:../login.php");
}

include("../config.php"); 

//search variables
$where = " WHERE tblJob.Deleted = 0";

if (!empty($_REQUEST['sortorder'])){	
    $sortorder = $_REQUEST['sortorder'];
}
?>

<div id="print-content">
    <div>
    
    <h1>
        <span id="pageTitle">CNC Jobs (incomplete)</span>
        <button type="button" id="print-btn" class="btn btn-default pull-right no-print"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Print</button>
        <img class="report-logo pull-right" src="img/report_logo.png" alt="Challenge Cabinets Logo" />
    </h1>
    </div>
    
    <div class="table">
    
        <?php
			$query = "SELECT Count(tblJob.JobID) FROM tblJob INNER JOIN tblJobMaterial ON tblJob.JobID = tblJobMaterial.JobID WHERE tblJobMaterial.CNCDateCompleted IS NULL AND tblJobMaterial.BoardQuantity <> 0 ORDER BY tblJob.JobID, tblJobMaterial.IsCarcass";
            
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
                    </tr>
                </thead>
                <tbody>
                    
                    <?php 
                    
                	$query = "SELECT tblJob.JobID, tblJob.JobAddress, tblJobMaterial.BoardDescription, tblJobMaterial.BoardType, tblJobMaterial.BoardQuantity, tblJobMaterial.CNCNotes FROM tblJob INNER JOIN tblJobMaterial ON tblJob.JobID = tblJobMaterial.JobID WHERE tblJobMaterial.CNCDateCompleted IS NULL AND tblJobMaterial.BoardQuantity <> 0 ORDER BY $sortorder";
					$result = $mysqli->query($query);
                    
                    $totaljobs = 0;
                    while($row = $result->fetch_array()){
                    	
                    	$totaljobs++;
                    ?>
                        <tr>
                            <td ><?php echo "<a href='../job.php?jobid=" . $row['JobID'] . "#cnc' target='_blank'>" . $row['JobID'] . "</a>" ?></td>
	                        <td ><?php echo $row['JobAddress'] ?></td>
                            <td><?php echo $row['BoardDescription'] ?></td>
                            <td><?php echo $row['BoardType'] ?></td>
                            <td><?php echo $row['BoardQuantity'] ?></td>
                            <td><?php echo nl2br($row['CNCNotes']) ?></td>
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