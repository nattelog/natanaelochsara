<?php 

## Main page for server

## Following functions should be here:

// This is the main package being sent to and from the server
class package {
  var $success;
  var $data;
}

// This should return a json-object with the guests from the database
function guest_get_list(){}

// This should return an int of the total amount of guests
function guest_count(){}

// This should return an int of the total amount of children
function children_count(){}

// This function should be the default error_handler taking some error-parameters
function custom_error(err){}

?>
