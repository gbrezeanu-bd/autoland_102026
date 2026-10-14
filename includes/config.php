<?php

/**
 * Here we define mysql connection
 * 
 * This version works from php 7.0 onward
 * 
 * @copyright (c) 2018, Marius Ionel <webmaster@grupnet.ro>
 */

session_start();
require_once '/home/autoland/public_html/includes/functions.php';
mb_internal_encoding('UTF-8');


// ////////////////////////////////////////////////////////////////////////////
// START USER CONFIGURATION
define('DEBUG', TRUE);
DEFINE('BASE_PATH', 'https://autolandromania.ro/'); // watch out!! force to www in htaccess redirection!


$hostname = "localhost";
$database = "autoland_autos1ww1a";
$username = "autoland_autos1";
$password = "p4zj2J48bG";

$email_host='autolandromania.ro';
$email_address="auto@autolandromania.ro";
$email_username=$email_address;
$email_password="MercedesSmart123";

/* configuratia veche, scoasa din uz 12 aprilie 2019 de Alex.
$email_host='autoland.ro';
$email_address="office@autoland.ro";
$email_username=$email_address;
$email_password="i_AT,34P+a;k";
*/

// END OF USER CONFIGURATION
// /////////////////////////////////////////////////////////////////////////////

ini_set('display_errors', false);

// Let's try to connect to the database
$connection = @new mysqli($hostname, $username, $password, $database);

// check for connection
if ($connection->connect_error) {
    echo "Error: " . $connection->connect_error;
    exit();
}

// set ptopper charset or we will have troubles later on
// $connection->set_charset('utf8');
$connection->set_charset('utf8mb4');


// Theme specific code
$page_title='';
$page_subtitle='';
$header_scripts='';
$footer_scripts='';
