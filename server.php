<?php 

## App: Guestlist
## File: database.php
## Author: Natanael Log, 2015
## Description: 
##      This file is for handling all communication
##      with the mySQL database.

    # ============ #
    # == CONFIG == #
    # ============ #

    # mySQL credentials
$db_servername = "localhost";
$db_username = "root";
$db_password = "losenord";
$db_name = "s_n_wedding";


    # =============== #
    # == REGISTERS == #
    # =============== #

$status_register = array("type" => "", "message" => "", "data" => array());



    # ============================= #
    # == STATUS & ERROR HANDLING == #
    # ============================= #

function php_error($error_level, $error_message, $error_file, $error_line){
    $e_report_levels = array(
        "2" => "Warning",
        "8" => "Notice",
        "256" => "User error",
        "512" => "User warning",
        "1024" => "User notice",
        "4096" => "Recoverable error",
        "8191" => "All"
    );
    $err_str = "<h3><span class='fa fa-warning'></span> $e_report_levels[$error_level]</h3>$error_message<br><small>$error_file [$error_line]</small>";
    send_status("php_error", $err_str);
    die();
}

set_error_handler("php_error");

function send_status($type, $message){
    $GLOBALS['status_register']['type'] = $type;
    $GLOBALS['status_register']['message'] = $message;
    //$GLOBALS['status_register']['user_register'] = $GLOBALS['user_register'];
    echo json_encode($GLOBALS['status_register']);
}



    # ==================== #
    # == MYSQL HANDLING == #
    # ==================== #

    # Create connection
$conn = new mysqli($db_servername, $db_username, $db_password, $db_name);

    # Check connection
if ($conn->connect_error) {
    send_status("php_error", $conn->connect_error);
    die();
}

?>