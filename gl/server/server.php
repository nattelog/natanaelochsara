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
    private $args;
    
    function __construct($s, $d, $c, $a){
        $this->success = $s;
        $this->data = $d;
        $this->caller = $c;
        $this->args = $a;
    }
    
    function to_json(){
        return json_encode(array(
            "success" => $this->success,
            "data" => $this->data,
            "caller" => $this->caller,
            "args" => $this->args
        ));
    }
}

// send_package is always the last function in the script
function send_package($success, $data, $caller, $args){
    if (!is_bool($success)) {
        $package = new Package(false, "$success is not a boolean.", $caller, $args);
    
    } else
        $package = new Package($success, $data, $caller, $args);
    
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
    return db_get_table($sql);
}

// This should return an int of the total amount of guests
function guest_count(){
    $sql = "SELECT * FROM GUEST";
    return db_count($sql);
}

// This should return an int of the total amount of children
function guest_children_count(){
    $sql = "SELECT * FROM GUEST WHERE is_child='1'";
    return db_count($sql);
}

function guest_exist($id){
    return db_record_exist($id, "GUEST");
}

// Returns true if successful
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
    $stmt->store_result();
    
    if ($stmt->affected_rows != 0)
        return $stmt->insert_id;
    else
        return false;
}

function guest_remove($id){
    db_remove_record($id, "GUEST");
}

function guest_remove_all(){
    db_remove_all_records("GUEST");
}

function guest_update_response($id, $option){
    return respone_update($id, $option);
}

function guest_update_household($guest, $household){
    pressume(is_int($guest) && is_int($household) && guest_exist($guest) && household_exist($household));
    
    $sql = "UPDATE GUEST SET household=? WHERE id=?";
    $stmt = db_prepare($sql);
    $stmt->bind_param("ii", $household, $guest);
    $stmt->execute();
    $stmt->store_result();
    
    return $stmt->affected_rows != 0;
}

function guest_get_household_id($guest){
    pressume(is_int($guest) && guest_exist($guest));
    
    return db_get_column_from_id("household", $guest, "HOUSEHOLD");
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
    return db_record_exist($id, "GUESTGROUP_REL");
}

function relation_type_exist($id){
    return db_record_exist($id, "GUESTGROUP_TYPE");
}

##
## RESPONSE FUNCTIONS
##

function response_get_id($description){
    $id = db_get_id_from_description($description, "RESPONSE");
    if (!$id) 
        fail("description $description has no id");
    return $id;
}

function response_count(){
    $id_coming = response_get_id("will_come");
    $id_not_coming = response_get_id("will_not_come");
    $sql = "SELECT * FROM GUEST WHERE response='$id_coming' OR response='$id_not_coming'";
    return db_count($sql);
}

function response_count_coming(){
    $id = response_get_id("will_come");
    $sql = "SELECT * FROM GUEST WHERE response='$id'";
    return db_count($sql);
}

function response_count_not_coming(){
    $id = response_get_id("will_not_come");
    $sql = "SELECT * FROM GUEST WHERE response='$id'";
    return db_count($sql);
}

function response_count_waiting(){
    $id = response_get_id("waiting");
    $sql = "SELECT * FROM GUEST WHERE response='$id'";
    return db_count($sql);
}

function response_get_options(){
    $sql = "SELECT * FROM RESPONSE";
    return db_get_table();
}

function response_already_set($guest){}

function response_exist($id){
    return db_record_exist($id, "RESPONSE");
}

// Options is the table ID
function response_update($guest, $option){
    pressume(is_int($guest) && is_int($option) && response_exist($option) && guest_exist($guest));
    
    $sql = "UPDATE GUEST SET response=? WHERE id=?";
    $stmt = db_prepare($sql);
    $stmt->bind_param("ii", $option, $guest);
    $stmt->execute();
    $stmt->store_result();
    
    return $stmt->affected_rows != 0;
}

// Gets an array, sets all individual guests inside it
function response_set_household($household){}

##
## HOUSEHOLD FUNCTIONS
##

function household_count(){
    $sql = "SELECT * FROM HOUSEHOLD";
    return db_count($sql);
}

function household_count_members($id){
    pressume(is_int($id));
    
    $sql = "SELECT * FROM GUEST WHERE household=?";
    $stmt = db_prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows;
}

function household_add($head, $address, $postal_code, $city){
    pressume(is_int($head) && is_string($address) && is_string($postal_code) && is_string($city));
    
    $sql = "INSERT INTO HOUSEHOLD(head_of_household, address, postal_code, city) VALUES (?, ?, ?, ?)";
    
    $stmt = db_prepare($sql);
    $stmt->bind_param("ssss", $head, $address, $postal_code, $city);
    
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->affected_rows != 0)
        return $stmt->insert_id;
    else return false;
}

function household_remove($id){
    db_remove_record($id, "HOUSEHOLD");
}

function household_remove_all(){
    db_remove_all_records("HOUSEHOLD");
}

function household_exist($id){
    return db_record_exist($id, "HOUSEHOLD");
}

// Returns a json-object containing all households with all people living there
function household_get_all(){
}

// Returns a json-object containing the household connected to $guest
function household_get($guest){
    pressume(is_int($guest) && guest_exist($guest));
    
    $sql = "SELECT * FROM GUEST ";
}

function household_get_members($id){
    pressume(is_int($id));
    
    $sql = "SELECT first_name, last_name, is_child FROM GUEST WHERE household=?";
    $stmt = db_prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    
    $result = array();
    $stmt->bind_result($first_name, $last_name, $is_child);
    while ($stmt->fetch())
        array_push($result, [$first_name, $last_name, $is_child]);
        
    if (empty($result))
        return false;
    else 
        return $result;
}

function household_update($id, $head, $address, $postal_code, $city){
    pressume(is_int($id) && is_int($head) && is_string($address) && is_string($postal_code) && is_string($city));
    
    if (!guest_exist($head))
        return false;
    
    $sql = "UPDATE HOUSEHOLD SET head_of_household=?, address=?, postal_code=?, city=? WHERE id=?";
    
    $stmt = db_prepare($sql);
    $stmt->bind_param("sssss", $head, $address, $postal_code, $city, $id);
    
    $stmt->execute();
    $stmt->store_result();
    
    return $stmt->affected_rows != 0;
}

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

function error_get_caller_args(){
    $stack = debug_backtrace();
    return $stack[2]['args'];
}

function fail($msg){
    send_package(false, $msg, error_get_caller(), error_get_caller_args());
    die();
}

function success($data){
    send_package(true, $data, error_get_caller(), error_get_caller_args());
}

function pressume($condition){
    if (!is_bool($condition)) {
        send_package(false, "$condition is not a boolean.", error_get_caller(), error_get_caller_args());
        die();
    
    } else if (!$condition) {
        send_package(false, "pressumption failed", error_get_caller(), error_get_caller_args());
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
