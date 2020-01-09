<?php

function validate_phone_number($phone) {

    // Allow +, - and . in phone number
    $filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
    // Remove "-" from number
    $phone_to_check = str_replace("-", "", $filtered_phone_number);
    // Check the lenght of number
    // This can be customized if you want phone number from a specific country
    $min_length = 10;
    $max_length = 14;
 
    if ( substr( $phone_to_check, 0, 1 ) === "0" ) {
       $min_length = 11;
       $max_length = 11;
    }
 
    if (strlen($phone_to_check) < $min_length || strlen($phone_to_check) > $max_length) {
       return false;
    } else {
       return true;
    }
}