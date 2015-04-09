<?php

## Main file for database handling

## Database should contain the following tables:

## GUEST - id, first_name, last_name, is_child, response, household, relation_to, relation_type, special_food

## STAFF - id, first_name, last_name, phone, task, has_confirmed

## TASK - id, description (kitchen, babysitter)

## HOUSEHOLD - id, head_of_household, address, postal_code, city

## GUESTGROUP_REL - id, description (groom, bride and mutual)

## GUESTGROUP_TYPE - id, description (family, friend)

## RESPONSE - id, description (waiting, will_come, will_not_come)

## SPECIAL_FOOD - id, guest, lactos, gluten, vegetarian, other

## USER - id, user_name, password

## LOG - id, ip, date, comment (e.g. 15-05-03: 192.168.0.1: "Försökte logga in med användarnamn 'reinelog' och lösenord 'blabla'")

##
## mySQL CONNECTION
##

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    db_open();
    guest_add("Natte'", "Log", 0, 1, 1);
    guest_get_list();
    db_close();
}
catch (mysqli_sql_exception $e){
    report($e);
}

##
## FUNCTIONS
##

function db_open(){
    // Credentials
    $servername = "localhost";
    $username = "root";
    $password = "losenord";
    $dbname = "guestlist";
        
    $GLOBALS["conn"] = new mysqli($servername, $username, $password, $dbname);
}

function db_close(){
    mysqli_close($GLOBALS["conn"]);
}

##
## UNSAFE QUERIES
##

function db_get_table($sql){
    $query = mysqli_query($GLOBALS["conn"], $sql);
    
    $result = array();
    
    while ($row = $query->fetch_array(MYSQLI_ASSOC))
        array_push($result, $row);
    
    return $result;
}

function db_get_row($sql){
    $query = mysqli_query($GLOBALS["conn"], $sql);
    
    if (!$query)
        fail($GLOBALS["conn"]->error);
    
    else if ($query->num_rows == 0)
        return false;
    
    return $query->fetch_array(MYSQLI_ASSOC);
}

function db_count($sql){
    $query = mysqli_query($GLOBALS["conn"], $sql);
    
    if (!$query)
        fail($GLOBALS["conn"]->error);
    
    return $query->num_rows;
}

##
## SAFE QUERIES
##

function db_prepare($sql){
    $stmt = $GLOBALS["conn"]->prepare($sql);
    if (!$stmt)
        fail($GLOBALS["conn"]->error);
    return $stmt;
}

function db_id_exist_safe($id, $table){
    $sql = "SELECT * FROM $table WHERE id=?";
    $stmt = db_prepare($sql);
    $stmt->bind_param("i", $id);
    
    if (!$stmt)
        fail($GLOBALS["conn"]->error);
    
    $stmt->execute();
    $stmt->store_result();
    
    return $stmt->num_rows != 0;
}

function db_last_id(){
    return $GLOBALS["conn"]->insert_id;
}

?>
