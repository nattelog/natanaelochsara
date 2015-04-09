<?php 

## Main page for server

require 'database.php';

##
## SESSION AND PACKAGE
##

// This is the main package being sent from the server
class Package {
    private $success;
    private $data;
    private $caller;
    
    function __construct($s, $d, $c){
        $this->success = $s;
        $this->data = $d;
        $this->caller = $c;
    }
    
    function to_json(){
        return json_encode(array(
            "success" => $this->success,
            "data" => $this->data,
            "caller" => $this->caller
        ));
    }
}

// send_package is always the last function in the script
function send_package($success, $data, $caller){
    if (!is_bool($success)) {
        $package = new Package(false, "$success is not a boolean.", $caller);
    
    } else
        $package = new Package($success, $data, $caller);
    
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
    success(db_get_table($sql));
}

// This should return an int of the total amount of guests
function guest_count(){
    $sql = "SELECT * FROM GUEST";
    success(db_count($sql));
}

// This should return an int of the total amount of children
function guest_children_count(){
    $sql = "SELECT * FROM GUEST WHERE is_child='1'";
    success(db_count($sql));
}

function guest_exist($id){
    return db_id_exists_safe($id, "GUEST");
}

function guest_add($first_name, $last_name, $is_child, $relation_to, $relation_type){
    pressume(relation_rel_exist($relation_to));
    pressume(relation_type_exist($relation_type));
    
    $sql = "INSERT INTO GUEST(first_name, last_name, is_child, response, household, relation_to, relation_type, special_food) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $response = response_get_id("waiting");
    $household = 0;
    $special_food = 0;
    
    $stmt = db_prepare($sql);
    $stmt->bind_param("ssiiiiii", $first_name, $last_name, $is_child, $response,  $household, $relation_to, $relation_type, $special_food);
    
    $stmt->execute();
    
}

function guest_remove($id){
    
}

##
## RELATION FUNCTIONS
##

function relation_get_rel(){
    $sql = "SELECT * FROM GUESTGROUP_REL";
    success(db_get_table($sql));
}

function relation_get_types(){
    $sql = "SELECT * FROM GUESTGROUP_TYPE";
    success(db_get_table($sql));
}

function relation_rel_exist($id){
    return db_id_exist_safe($id, "GUESTGROUP_REL");
}

function relation_type_exist($id){
    return db_id_exist_safe($id, "GUESTGROUP_TYPE");
}

##
## RESPONSE FUNCTIONS
##

function response_get_id($description){
    $sql = "SELECT * FROM RESPONSE WHERE description='$description'";
    $row = db_get_row($sql);
    if (!$row)
        fail("no id corresponding to " . $description);
    return $row["id"];
}

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
    fail($msg);
}

set_error_handler("custom_error");

function error_get_caller(){
    $stack = debug_backtrace();
    return $stack[2]['function'];
}

function fail($msg){
    send_package(false, $msg, error_get_caller());
    die();
}

function success($data){
    send_package(true, $data, error_get_caller());
}

function pressume($condition){
    if (!is_bool($condition)) {
        send_package(false, "$condition is not a boolean.", error_get_caller());
        die();
    
    } else if (!$condition) {
        send_package(false, "pressumption failed", error_get_caller());
        die();
    }
}

function report($e){
    fail($e->__toString());
}

##
## LOGGING
##

function log_record(){}

function log_get_records(){}

?>
