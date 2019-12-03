<?php
    require_once('includes/config.php');
    require('layout/header.php');
    $title = 'Login';
?>

<form role="form" id="login" action="" method="post">
<input type="text" name="username" id="name" placeholder="username"><br>
<input type="password" name="password" id="password" placeholder="password"><br>
<div id="submitButton">
    <input type="submit" name="submit" value="Login">
    <button type="button"><a href="register.php">Register</a></button>
</div>
</form>
