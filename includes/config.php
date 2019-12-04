<?php
ob_start();
if(session_status() != PHP_SESSION_ACTIVE)
{
    session_start();
}

$config = parse_ini_file('config.ini');

$noreply_email_addr .= 'no_reply@uncevents.online';

function emailNotifaction($message, $subject, $to, $from)
{
  $headers = "From: ".$from;
  // use wordwrap() if lines are longer than 70 characters
  $msg = wordwrap($message, 70);
  // send email
  mail($to, $subject, $msg, $headers);
}

//set timezone
date_default_timezone_set('America/Denver');

//database credentials
define('DBHOST', 'localhost');
define('DBUSER', 'u800519350_gLpRH');
define('DBPASS', 'cs350');
define('DBNAME', 'u800519350_LBuOL');

//application address
define('DIR', 'http://localhost');
define('SITEEMAIL', 'webmaster@uncevents.online');
ini_set('display_errors', 1);
error_reporting(E_ERROR);

try {
  //create PDO connection
  $db = new PDO("mysql:host=".DBHOST.";charset=utf8mb4;dbname=".DBNAME, DBUSER, DBPASS);
  //$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);//Suggested to uncomment on production websites
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Suggested to comment on production websites
  $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
	//show error
  echo '<p class="bg-danger">'.$e->getMessage().'</p>';
  exit;
}

//include the user class, pass in the database connection
include('classes/user.php');
//include('classes/phpmailer/mail.php');
$user = new User($db);
?>
