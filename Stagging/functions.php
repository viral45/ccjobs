<?php

function sec_session_start() {
        $session_name = 'sec_session_id'; // Set a custom session name
        $secure = false; // Set to true if using https.
        $httponly = true; // This stops javascript being able to access the session id. 
 
        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies. 
        $cookieParams = session_get_cookie_params(); // Gets current cookies params.
        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
        session_name($session_name); // Sets the session name to the one set above.
        session_start(); // Start the php session
        session_regenerate_id(); // regenerated the session, delete the old one.  
}

function get_name_from_id($userid, $mysqli){
        if (isset($userid)){
                if ($stmt = $mysqli->prepare("SELECT FullName FROM tblUser WHERE UserID = ? LIMIT 1")) { 
                        $stmt->bind_param('s', $userid);
                        $stmt->execute();
                        $stmt->store_result();
                        $stmt->bind_result($fullname);
                        $stmt->fetch();
                }
        }
        return $fullname;
}

function get_url_hash(){
        $urlhash = "";

        if ($_SESSION['is_draftsman'] <> 0)
                $urlhash = "#draftsman";
        else if ($_SESSION['is_cnc'] <> 0)
                $urlhash = "#cnc";
        else if ($_SESSION['is_edging'] <> 0)
                $urlhash = "#edging";
        else if ($_SESSION['is_assembler'] <> 0)
                $urlhash = "#assembler";
        else if ($_SESSION['is_installer'] <> 0)
                $urlhash = "#installer";

        return $urlhash;
}

function jobtask_checklist_complete($jobid, $taskid, $mysqli){

        $query = "SELECT * FROM tblJobTask WHERE JobID = $jobid AND TaskID = $taskid";
        $result = $mysqli->query($query);
        $row = $result->fetch_array();

        $checkarray = ["CopyPlans", "QuickClips", "WhiteCaps", "SinkCutouts", "HotplateCutouts", "BasesPelmets", "AppliancesFitted", "CutleryTrays", "WorkersName", "CabinetLabelled", "StickersRemoved", "AllAccessories", "HandlesMounted", "BumpOns", "MeasureOverallSizes", "OverheadCleats", "KitchenInspected", "SoftClosers", "BlocksKickboards", "CheckRails", "KickboardsNumbered", "DishwasherAngle", "Templates"];
        $uncheckedflag = false;

        foreach ($checkarray as $check){
                if ($row[$check] == 0)
                        $uncheckedflag = true;
        }

        return $uncheckedflag;
}

function jobtask_missing_items($jobid, $taskid, $mysqli){

        $query = "SELECT MissingItems, MissingItemsComplete FROM tblJobTask WHERE JobID = $jobid AND TaskID = $taskid";
        $result = $mysqli->query($query);
        $row = $result->fetch_array();

        $missingflag = false;

        if (!empty($row['MissingItems']) && $row['MissingItemsComplete'] == 0)
                $missingflag = true;

        return $missingflag;
}

function jobtaskinstall_checklist_complete($jobid, $taskid, $mysqli){

        $query = "SELECT * FROM tblJobTaskInstall WHERE JobID = $jobid AND TaskID = $taskid";
        $result = $mysqli->query($query);
        $row = $result->fetch_array();

        $checkarray = ["Gapped", "Capped", "AdjustedDoorsDrawers", "BenchtopsTemplatesInstalled", "BasinsInstalled", "AllLevelsChecked", "OvenCleats", "CabinetsCleaned"];
        $uncheckedflag = false;

        foreach ($checkarray as $check){
                if ($row[$check] == 0)
                        $uncheckedflag = true;
        }

        return $uncheckedflag;
}

function jobtaskinstall_missing_items($jobid, $taskid, $mysqli){

        $query = "SELECT MissingItems, MissingItemsComplete FROM tblJobTaskInstall WHERE JobID = $jobid AND TaskID = $taskid";
        $result = $mysqli->query($query);
        $row = $result->fetch_array();

        $missingflag = false;

        if (!empty($row['MissingItems']) && $row['MissingItemsComplete'] == 0)
                $missingflag = true;

        return $missingflag;
}


function jobtaskdraft_missing_items($jobid, $taskid, $mysqli){

        $query = "SELECT MissingItems, MissingItemsComplete FROM tblJobTaskDraft WHERE JobID = $jobid AND TaskID = $taskid";
        $result = $mysqli->query($query);
        $row = $result->fetch_array();

        $missingflag = false;

        if (!empty($row['MissingItems']) && $row['MissingItemsComplete'] == 0)
                $missingflag = true;

        return $missingflag;
}

function jobtaskmaint_missing_items($jobid, $taskid, $mysqli){
        
        $query = "SELECT MissingItems, MissingItemsComplete FROM tblJobTaskMaint WHERE JobID = $jobid AND TaskID = $taskid";
        $result = $mysqli->query($query);
        $row = $result->fetch_array();

        $missingflag = false;

        if (!empty($row['MissingItems']) && $row['MissingItemsComplete'] == 0)
                $missingflag = true;

        return $missingflag;
}

function file_newname($path, $filename){
	
	$ext = "";
    if ($pos = strrpos($filename, '.')) {
           $name = substr($filename, 0, $pos);
           $ext = substr($filename, $pos);
    } else {
           $name = $filename;
    }
	
	$name = preg_replace("/[^a-zA-Z0-9_\-\s]/", "", $name);

    $newpath = $path.'/'.$filename;
    $newname = $name . $ext;
    $counter = 0;
    while (file_exists($newpath)) {
           $newname = $name .'_'. $counter . $ext;
           $newpath = $path.'/'.$newname;
           $counter++;
     }

    return $newname;
}

function create_thumbnail($originalimage, $thumbw, $thumbh, $destination){	
		 
	$nh = $thumbh;
	$nw = $thumbw;
	
	$size = getImageSize($originalimage);
	$w = $size[0];
	$h = $size[1];
  
	// Applying calculations to dimensions of the image
	$ratio = $h / $w;
	$nratio = $nh / $nw; 

	if($ratio > $nratio)
	{
		$x = intval($w * $nh / $h);
		if ($x < $nw)
			$nh = intval($h * $nw / $w);
		else
			$nw = $x;
	}
	else
	{
		$x = intval($h * $nw / $w);
		if ($x < $nh)
			$nw = intval($w * $nh / $h);
		else
			$nh = $x;
	}
		
	$x_mid = $nw/2;  //horizontal middle
    $y_mid = $nh/2; //vertical middle
  
	//draw the image
	$src_img = imagecreatefromjpeg($originalimage);
	$dst_img = imagecreatetruecolor($nw,$nh);
	imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $nw, $nh, $w, $h);//resizing the image
	
	// Making the final cropped thumbnail
	$view_img = imagecreatetruecolor($thumbw, $thumbh);
	imagecopy($view_img, $dst_img, 0, 0, ($x_mid-($thumbw/2)), ($y_mid-($thumbh/2)), $nw, $nh);
	
	imagejpeg($view_img,$destination,80);
	imagedestroy($src_img);
	imagedestroy($dst_img);
	imagedestroy($view_img);


}

function resize_image($originalimage, $maxDim){
	list($width, $height, $type, $attr) = getimagesize( $originalimage );
	if ( $width > $maxDim || $height > $maxDim ) {
		$target_filename = $originalimage;
		$ratio = $width/$height;
		if( $ratio > 1) {
			$new_width = $maxDim;
			$new_height = $maxDim/$ratio;
		} else {
			$new_width = $maxDim*$ratio;
			$new_height = $maxDim;
		}
		$src = imagecreatefromjpeg( $originalimage );
		$dst = imagecreatetruecolor( $new_width, $new_height );
		imagecopyresampled( $dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

		imagedestroy( $src );
		imagejpeg( $dst, $target_filename,80 );
		imagedestroy( $dst );
	}
}
?>