<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

if (isset($_REQUEST["page"])) { $page  = $_REQUEST["page"]; } else { $page=1; };  

?>

<div class="table-responsive">

	<?php
				
		$query = "SELECT COUNT(UserID) FROM tblAdminUser";
		$result = $mysqli->query($query);
		$row = $result->fetch_array(); 
		
		if ($row[0] > 0 ){
	?> 
    
        <table class="table table-striped">
            <thead>
                <tr>
                  <th>Name</th>
                  <th width="80px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                
                <?php 
                $query = "SELECT UserID, FullName FROM tblAdminUser ORDER BY FullName";
                $result = $mysqli->query($query);
                
                while($row = $result->fetch_array()){
                ?>
                    <tr>
                        <td><?php echo $row['FullName'] ?></td>
                        <td>                            
                            <button type="button" value="<?php echo $row['UserID']; ?>" class="btn btn-warning btn-xs edit-btn" data-toggle="tooltip" data-placement="top" title="Edit"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                            <button type="button" value="<?php echo $row['UserID']; ?>" class="btn btn-danger btn-xs delete-btn" data-toggle="tooltip" data-placement="top" title="Delete"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                        </td>
                    </tr>
                    
                <?php } ?>
            </tbody>   
        </table>
        
    <?php } else { echo "No records were found."; } ?>
    
</div>

