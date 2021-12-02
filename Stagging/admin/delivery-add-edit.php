<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

if (isset($_POST['action']))
{
	if ($_POST['action'] == "edit"){
		$deliveryid = isset($_POST['deliveryid']) ? $_POST['deliveryid'] : die('ERROR: Delivery ID not found.');

		if ($stmt = $mysqli->prepare("SELECT DeliveryID, JobID, Description, DeliveryDate, SortOrder, Notes FROM tblDelivery WHERE DeliveryID = ? LIMIT 1")) { 
			$stmt->bind_param('i', $deliveryid);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($formdeliveryid, $formjobid, $formdescription, $formdeliverydate, $formsortorder, $formnotes);
			$stmt->fetch();
		}
	}
}
?>

<form id='delivery-form' action='#' method='post'>
    <input type="hidden" id="action" name="action" value="<?php if (isset($_POST['action'])) { echo $_POST['action']; } ?>">
    <input type="hidden" id="deliveryid" name="deliveryid" value="<?php if (isset($_POST['deliveryid'])) { echo $_POST['deliveryid']; } ?>">

	<div class="form-group">
		<label for="inputJobID">Job</label>
		<div class="input-group">
			<select id="inputJobID" name="inputJobID" class="form-control selectpicker jobgroup">
				<option value="">None</option>
				<?php
					$query = "SELECT JobID, JobAddress FROM tblJob WHERE Deleted = 0 ORDER BY JobAddress";
					$result = $mysqli->query($query);			

					while($row = $result->fetch_array())
					{	
						if (isset($formjobid))
							$selected = ($row['JobID'] == $formjobid) ? " SELECTED" : ""; 	
						else
							$selected = "";

						echo "<option value=" . $row['JobID'] . " $selected>" . $row['JobAddress'] . "</option>";
					}
				?>
			</select>
			<span class="input-group-btn">
				<button id="viewjob-btn" class="btn btn-primary" type="button">View</button>
			</span>
		</div>

	</div>

	<div class="form-group">
		<label for="inputBuilder">Builder</label>
		<input type="text" class="form-control" id="inputBuilder" name="inputBuilder" disabled="disabled">
	</div>
		
	<div class="form-group">
		<label for="inputDescription">Description (if no linked job)</label>
		<input type="text" class="form-control jobgroup" id="inputDescription" name="inputDescription"  autocomplete="off" placeholder="Description" value="<?php if (isset($formdescription)) { echo htmlspecialchars($formdescription, ENT_QUOTES); } ?>">
	</div>

	<div class="form-group">
		<label for="inputDeliveryDate">Date</label>
		<input type="text" class="form-control" style="position: relative; z-index: 5;" id="inputDeliveryDate" name="inputDeliveryDate" required autocomplete="off" placeholder="Date" date="true" value="<?php if (isset($formdeliverydate)) { echo htmlspecialchars(date("d-m-Y", strtotime($formdeliverydate)), ENT_QUOTES); } ?>">
	</div>
	
	<div class="form-group">
		<label for="inputNotes">Notes</label>
		<textarea class="form-control" id="inputNotes" name="inputNotes" rows="6"><?php if (isset($formnotes)) { echo htmlspecialchars($formnotes, ENT_QUOTES); } ?></textarea>
	</div>	

    <button type="submit" id="save-btn" class="btn btn-primary">Save</button>
	<button type="button" id="return-btn" class="btn btn-warning"  data-dismiss="modal">Return</button>
	
	<?php if ($_POST['action'] == "edit"){ ?>
	<button type="button" id="delete-btn" class="btn btn-danger pull-right" value="<?php if (isset($formdeliveryid)) { echo $formdeliveryid; } ?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete</button> 
	<?php } ?>
</form>