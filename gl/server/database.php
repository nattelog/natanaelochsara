<?php 

## Main file for database handling

## Database should contain the following tables:

## GUEST - id, first_name, last_name, is_child, response, household, relation_to, relation_type, allergy

## STAFF - id, first_name, last_name, phone, task, has_confirmed

## HOUSEHOLD - id, head_of_household, address, postal_code, city

## GUESTGROUP_REL - id, description (groom, bride and mutual)

## GUESTGROUP_TYPE - id, description (family, friend)

## RESPONSE - id, description (waiting, will_come, will_not_come)

## USER - id, user_name, password

## LOG - id, ip, date, comment (e.g. 15-05-03: 192.168.0.1: "Försökte logga in med användarnamn 'reinelog' och lösenord 'blabla'")

?>
