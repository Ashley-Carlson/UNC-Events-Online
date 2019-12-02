<?php
ob_start();
if(session_status() != PHP_SESSION_ACTIVE)
{
    session_start();
}

$config = parse_ini_file('config.ini');

$noreply_email_addr .= 'no_reply@uncevents.online';

function emailNotifaction($message, $subject, $address, $from)
{
  $headers = "From: ".$from;
  // the message
  $msg = $message;
  // use wordwrap() if lines are longer than 70 characters
  $msg = wordwrap($msg, 70);
  // send email
  mail($address, $subject, $msg, $headers);
}

//set timezone
date_default_timezone_set('America/Denver');

//database credentials
define('DBHOST', '192.185.17.37');
define('DBUSER', 'eragon57_antiwp');
define('DBPASS', 'GJNjKyXW66BhoVayVV');
define('DBNAME', 'eragon57_antiwp');

//application address
define('DIR', 'http://localhost');
define('SITEEMAIL', 'example@antiwp.dragonfirecomputing.com');

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
