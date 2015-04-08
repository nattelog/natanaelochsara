<?php 

require 'database.php';

## Main page for server

##
## SESSION AND PACKAGE
##

// This is the main package being sent to and from the server
class Package {
  var $success;
  var $data;
}

function send_package($success, $data){}

function session_is_on(){}

function session_set($username){}

function session_kill(){}

##
## GUEST FUNCTIONS
##

// This should return a json-object with the guests from the database
function guest_get_list(){}

// This should return an int of the total amount of guests
function guest_count(){}

// This should return an int of the total amount of children
function guest_children_count(){}

function guest_add(){}

function guest_remove(){}

##
## RESPONSE FUNCTIONS
##

function response_count_total(){}

function response_count_coming(){}

function response_count_not_coming(){}

function response_get_options(){}

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

// Uses $_SESSION for $username
function user_logout(){}

function user_wrong_credentials($username, $password){}

##
## ERROR FUNCTIONS
##

set_error_handler("custom_error");

// This function should be the default error_handler taking some error-parameters
function custom_error(err){}

?>
