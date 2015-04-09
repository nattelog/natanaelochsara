<?php 

## Main page for server

require 'database.php';

set_error_handler("custom_error");

##
## SESSION AND PACKAGE
##

// This is the main package being sent to and from the server
class Package {
    private $success;
    private $data;
    
    function __construct($s, $d){
        $this->success = $s;
        $this->data = $d;
    }
    
    function to_json(){
        return json_encode(array(
            "success" => $this->success,
            "data" => $this->data
        ));
    }
}

function send_package($success, $data){
    if ($success !== true && $success !== false)
        $package = new Package(false, "Wrong way of sending package...");
    else
        $package = new Package($success, $data);
    echo $package->to_json();
}

function session_is_on(){
    return isset($_SESSION["username"]);
}

function session_set($username){
    session_start();
    $_SESSION["username"] = $username;
}

function session_kill(){
    session_unset();
    session_destroy();
}

##
## GUEST FUNCTIONS
##

// This should return a json-object with the guests from the database
function guest_get_list(){
    $sql = "SELECT * FROM GUEST";
    send_package(true, db_get_table($sql));
}

// This should return an int of the total amount of guests
function guest_count(){
    $sql = "SELECT * FROM GUEST";
    send_package(true, db_count($sql));
}

// This should return an int of the total amount of children
function guest_children_count(){
    $sql = "SELECT * FROM GUEST WHERE is_child='1'";
    send_package(true, db_count($sql));
}

function guest_add($first_name, $last_name, $is_child, $relation_to, $relation_type){}

function guest_remove(){}

##
## RESPONSE FUNCTIONS
##

function response_count_total(){}

function response_count_coming(){}

function response_count_not_coming(){}

function response_get_options(){}

function response_already_set($guest){}

// Options is the table ID
function response_set($guest, $option){}

// Gets an array, sets all individual guests inside it
function response_set_household($household){}

##
## HOUSEHOLD FUNCTIONS
##

function household_count(){}

// Returns a json-object containing all households with all people living there
function get_households(){}

// Returns a json-object containing the household connected to $guest
function get_household($guest){}

##
## USER FUNCTIONS
##

function user_login($username, $password){}

// Uses $_SESSION as $username
function user_logout(){}

function user_wrong_credentials($username, $password){}

##
## ERROR FUNCTIONS
##

// This function should be the default error_handler taking some error-parameters
function custom_error($error_level, $error_message, $error_file, $error_line){
    
    $error_map = array(
        "2" => "Warning",
        "8" => "Notice",
        "256" => "User error",
        "512" => "User warning",
        "1024" => "User notice",
        "4096" => "Recoverable error",
        "8191" => "All"
    );
    
    $msg = "<h3>$error_map[$error_level]</h3><p>$error_message<br>$error_file [$error_line]</p>";
    send_package(false, $msg);
}

##
## LOGGING
##

function log_record(){}

function log_get_records(){}

?>
