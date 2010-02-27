<?php

/**
 * @file
 * Add a user to JIRA, using a random password
 *
 * Usage:
 * $ php -f jira-add-user.php username fullname email [password]
 */
include_once('./bootstrap.php');

// Check we have the right number of arguments before proceeding
if ($argc < 4) {
    $out[] = ($argc - 1) ." arguments supplied, but at least 3 are required";
    $out[] = "Format: php -f jira-add-user.php username \"Full name\" email@address.com password[optional]";
    dump();
    exit();
}

$new_username = $argv[1];
$new_password = $argv[4] ? $argv[4] : user_password();
$new_fullname = $argv[2];
$new_email = $argv[3];

try {
    $result = $client->createUser($token, $new_username, $new_password, $new_fullname, $new_email);
    $out[] = "Created user $username";
    $out[] = "Result:";
    $out[] = print_r($result, true);
}
catch (SoapFault $fault) {
    $out[] = "Error creating user $username";
    $out[] = $fault;
}

dump();

?>