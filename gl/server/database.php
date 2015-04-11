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
    $guest = guest_add("Natte", "Log", 0, 1, 2);
    $guest2 = guest_add("Sara", "Danell", 0, 1, 2);
    $household = household_add($guest, "Bielkegatan 4", "582 21", "Linköping");
    guest_update_household($guest, $household);
    guest_update_household($guest2, $household);
    echo guest_get_household_id($guest);
    //guest_remove_all();
    //household_remove_all();
    //response_set(100, 2);
    //response_set(101, 2);
    //response_set(102, 2);
    //guest_get_list();
    echo "<br>Total guests: " . guest_count();
    echo "<br>Coming: " . response_count_coming();
    echo "<br>Not coming: " . response_count_not_coming();
    echo "<br>Waiting: " . response_count_waiting();
    echo "<br>Households: " . household_count();
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
    pressume(is_string($sql));
    
    $query = mysqli_query($GLOBALS["conn"], $sql);
    
    $result = array();
    
    while ($row = $query->fetch_array(MYSQLI_ASSOC))
        array_push($result, $row);
    
    return $result;
}

function db_get_row($sql){
    pressume(is_string($sql));
    
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
    pressume(is_string($sql));
    
    $stmt = $GLOBALS["conn"]->prepare($sql);
    if (!$stmt)
        fail($GLOBALS["conn"]->error);
    return $stmt;
}

function db_record_exist($id, $table){
    pressume(is_int($id));
    pressume(is_string($table));
    
    $sql = "SELECT * FROM $table WHERE id=?";
    $stmt = db_prepare($sql);
    $stmt->bind_param("i", $id);
    
    if (!$stmt)
        fail($GLOBALS["conn"]->error);
    
    $stmt->execute();
    $stmt->store_result();
    
    return $stmt->num_rows != 0;
}

function db_remove_record($id, $table){
    pressume(is_int($id) && is_string($table));
    if (db_record_exist($id, $table)) {
        $sql = "DELETE FROM $table WHERE id=?";
        $stmt = db_prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

function db_remove_all_records($table){
    pressume(is_string($table));
    
    $sql = "SELECT * FROM $table";
    $table_t = db_get_table($sql);
    
    foreach($table_t as $record)
        db_remove_record(intval($record["id"]), $table);
}

function db_get_id_from_description($description, $table){
    pressume(is_string($description));
    pressume(is_string($table));
    
    $sql = "SELECT * FROM $table WHERE description=?";
    $stmt = db_prepare($sql);
    $stmt->bind_param("s", $description);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows == 0 || $stmt->num_rows > 1)
        return false;
    
    $stmt->bind_result($id, $descr);
    if ($stmt->fetch())
        return intval($id);
    else 
        return false;
}

function db_get_column_from_id($column, $id, $table){
    pressume(is_string($column) && is_int($id) && is_string($table));

    $sql = "SELECT '$column' FROM $table WHERE id=?";
    $stmt = db_prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows == 0 || $stmt->num_rows > 1)
        return false;
    
    $stmt->bind_result($data);
    if ($stmt->fetch())
        return $data;
    else
        return false;
}

?>
