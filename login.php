<?php
    require_once('includes/config.php');
    require('layout/header.php');
    $title = 'Login';
?>

<form role="form" id="login" action="" method="POST">
    <div class="container">
        <body class="login">
            <h1 class="title">Login</h1>
            <hr>
            <b>Username: </b><br>
            <input type="text" name="username" id="name" placeholder="username"><br>

            <b>Password: </b><br>
            <input type="password" name="password" id="password" placeholder="password"><br>
            <div id="submitButton">
                <input type="submit" name="submit" value="Login" id="login-button">
            </div>
            <p id="register-link">Don't have an account? Click <a href="register.php">here</a> to register.</p>
        </body>
    </div>
</form>
