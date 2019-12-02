<?php
include 'includes/config.php';
if(!empty($_GET['verify']) && isset($_GET['verify']))
{
    $verify_string=$_GET['verify'];
    $stmt = $db->prepare("SELECT EXISTS(SELECT 1 FROM user WHERE verify_string = :verify_string) as exist");
    $stmt -> execute(array(':verify_string' => $verify_string));
    $exists = $stmt -> fetch(PDO::FETCH_ASSOC);

    if ($exists['exist'] == 1)
    {
        $stmt = $db->prepare("UPDATE user SET is_inactive = 0 WHERE verify_string = :verify_string");
        $stmt -> execute(array(':verify_string' => $verify_string));

        echo('<p>Your account has been verified. Click below to log in and begin using uncevents.online.</p>
        <a href="https://uncevents.online/login.php">Login Here</a>');
    }
    else
    {
        $msg ="Wrong activation code.";
    }
}
?>
