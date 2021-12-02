<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['is_maint'] <> 1){
	header("location:index.php");
	die();
}

include("config.php"); 

$today = date('d-m-Y');

$weekstart = (!empty($_REQUEST["weekstart"])) ? $_REQUEST["weekstart"] : date('d-m-Y');

$mondaydate = date('d-m-Y',strtotime("monday this week", strtotime($weekstart)));
$tuesdaydate = date('d-m-Y',strtotime("tuesday this week", strtotime($weekstart)));
$wednesdaydate = date('d-m-Y',strtotime("wednesday this week", strtotime($weekstart)));
$thursdaydate = date('d-m-Y',strtotime("thursday this week", strtotime($weekstart)));
$fridaydate = date('d-m-Y',strtotime("friday this week", strtotime($weekstart)));

$datearray = array($mondaydate, $tuesdaydate, $wednesdaydate, $thursdaydate, $fridaydate);

echo "<h3>WEEK " . $mondaydate . " to " . $fridaydate . "</h3>";

?>

<div class="table-responsive">

    <table id="caltable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th style="width: 20%">MONDAY<br><?php echo $mondaydate; ?></th>
                <th style="width: 20%">TUESDAY<br><?php echo $tuesdaydate; ?></th>
                <th style="width: 20%">WEDNESDAY<br><?php echo $wednesdaydate; ?></th>
                <th style="width: 20%">THURSDAY<br><?php echo $thursdaydate; ?></th>
                <th style="width: 20%">FRIDAY<br><?php echo $fridaydate; ?></th>
            </tr>
        </thead>

        <tbody>

            <tr>
                <?php 
                    foreach ($datearray as $day){ 
                        echo "<td class='entry' data-date='" . date('Y-m-d',strtotime($day)) . "'>";
                        echo "<button class='btn btn-xs btn-primary pull-right add-entry-btn' data-maintschedule-date='$day'>+</button>";
                        $maintschedulequery = "(SELECT tblMaintSchedule.MaintScheduleID, tblJob.JobAddress, tblJob.JobID, tblMaintSchedule.Description, tblMaintSchedule.SortOrder As Sort FROM tblJob INNER JOIN tblMaintSchedule ON tblJob.JobID = tblMaintSchedule.JobID WHERE tblMaintSchedule.ScheduleDate = '" . date('Y-m-d',strtotime($day)) . "') UNION ALL (SELECT tblMaintSchedule.MaintScheduleID, tblMaintJob.JobAddress, tblMaintJob.MaintJobID, tblMaintSchedule.Description, tblMaintSchedule.SortOrder As Sort FROM tblMaintJob INNER JOIN tblMaintSchedule ON tblMaintJob.MaintJobID = tblMaintSchedule.MaintJobID WHERE tblMaintSchedule.ScheduleDate = '" . date('Y-m-d',strtotime($day)) . "') UNION ALL (SELECT MaintScheduleID, '' As JobAddress, '' As JobID, Description, SortOrder As Sort FROM tblMaintSchedule WHERE JobID IS NULL and MaintJobID IS NULL AND ScheduleDate = '" . date('Y-m-d',strtotime($day)) . "') ORDER BY Sort";
                        $maintscheduleresult = $mysqli->query($maintschedulequery);
                        
                        while($maintschedulerow = $maintscheduleresult->fetch_array()){
                            if (!empty($maintschedulerow['JobID'])){
                                echo "<div class='alert alert-warning calendar-entry' data-action='edit' data-maintschedule-id='" . $maintschedulerow['MaintScheduleID'] . "'><button type='button' class='close delete-btn' aria-label='Close' value='" . $maintschedulerow['MaintScheduleID'] . "'><span aria-hidden='true'>&times;</span></button>" . $maintschedulerow['JobAddress'] . "</div>";                                  
                            }
                            else{
                                echo "<div class='alert alert-warning calendar-entry' data-action='edit' data-maintschedule-id='" . $maintschedulerow['MaintScheduleID'] . "'><button type='button' class='close delete-btn' aria-label='Close' value='" . $maintschedulerow['MaintScheduleID'] . "'><span aria-hidden='true'>&times;</span></button>" . $maintschedulerow['Description'] . "</div>";                                    
                            }                           
                        }
                        
                        echo "</td>";              
                ?>

                <?php } ?>
            
            </tr>

        </tbody>

    </table>
    
</div>


<button class='btn btn-primary week-btn' value="<?php echo date('d-m-Y',strtotime("monday last week", strtotime($weekstart))) ?>">
    <span aria-hidden="true">&laquo;</span> Previous Week
</button>

<button class='btn btn-primary week-btn' value="<?php echo date('d-m-Y',strtotime("monday next week", strtotime($weekstart))) ?>">
     Next Week <span aria-hidden="true">&raquo;</span>
</button>

