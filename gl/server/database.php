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

    # mySQL credentials
$servername = "localhost";
$username = "root";
$password = "losenord";
$dbname = "guestlist";

    # Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

    # Check connection
if ($conn->connect_error)
    send_package(false, $conn->connect_error);

guest_get_list();

guest_count();

guest_children_count();

db_close();

##
## FUNCTIONS
##

function db_get_table($sql){
    $query = mysqli_query($GLOBALS["conn"], $sql);
    
    if (!$query)
        send_package(false, $GLOBALS["conn"]->error);
    
    $result = array();
    
    while ($row = $query->fetch_array(MYSQLI_ASSOC))
        array_push($result, $row);
    
    return $result;
}

function db_get_row($sql){
    $query = mysqli_query($GLOBALS["conn"], $sql);
    
    if (!$query)
        send_package(false, $GLOBALS["conn"]->error);
    
    else if ($query->num_rows == 0)
        return false;
    
    return $query->fetch_array(MYSQLI_ASSOC);
}

function db_count($sql){
    $query = mysqli_query($GLOBALS["conn"], $sql);
    
    if (!$query)
        send_package(false, $GLOBALS["conn"]->error);
    
    return $query->num_rows;
}

function db_close(){
    mysqli_close($GLOBALS["conn"]);
}

?>
