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

function get_job_builder($jobid, $mysqli){
        if (isset($jobid)){
                if ($stmt = $mysqli->prepare("SELECT Builder FROM tblJob WHERE JobID = ? LIMIT 1")) { 
                        $stmt->bind_param('i', $jobid);
                        $stmt->execute();
                        $stmt->store_result();
                        $stmt->bind_result($builder);
                        $stmt->fetch();
                }
        }
        return $builder;  
}
?>