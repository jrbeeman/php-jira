<?php

/**
 * Setup
 */
include_once('./config.php');
$client = new SoapClient($wsdl);
$token = '';

try {
    $token = $client->login($username, $password);
}
catch (SoapFault $fault) {
    $out[] = "Error logging in to JIRA";
    $out[] = $fault;
}

global $out;
$out = array();

function dump() {
    global $out;
    print implode("\n", $out);
    print "\n";
}

/**
 * Generate a random alphanumeric password.
 *
 * From Drupal: http://api.drupal.org/api/function/user_password/6
 */
function user_password($length = 10) {
  // This variable contains the list of allowable characters for the
  // password. Note that the number 0 and the letter 'O' have been
  // removed to avoid confusion between the two. The same is true
  // of 'I', 1, and 'l'.
  $allowable_characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';

  // Zero-based count of characters in the allowable list:
  $len = strlen($allowable_characters) - 1;

  // Declare the password as a blank string.
  $pass = '';

  // Loop the number of times specified by $length.
  for ($i = 0; $i < $length; $i++) {

    // Each iteration, pick a random character from the
    // allowable string and append it to the password:
    $pass .= $allowable_characters[mt_rand(0, $len)];
  }

  return $pass;
}


?>