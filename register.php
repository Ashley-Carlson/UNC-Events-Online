<?php
require_once('includes/config.php');
$title = "Register";
require('layout/header.php');
if ($user->is_logged_in()) {
	header("Location: index.php");
}
$errors = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST))
{
	$acct_type = isset($_POST['is_faculty']) ? 0 : 1;
  $stmt = $db->prepare("SELECT EXISTS(SELECT 1 FROM user WHERE username = :username) as exist");
  $stmt -> execute(array(':username' => $_POST['username']));
  $exists = $stmt -> fetch(PDO::FETCH_ASSOC);
  if ($exists['exist'] == 1)
  {
		$errors[] = "This username is already taken";
  }
	if ($_POST['passwd'] != $_POST['verify'])
	{
		$errors[] = "Passwords do not match!";
	}

	if (count($errors) == 0)
	{
		$hash_options = [
			'cost' => 12,
		];
		$hash = $user->password_hash($_POST['passwd'], PASSWORD_DEFAULT, $hash_options);
        $verify_string = md5((string)time());
		$stmt = $db->prepare("INSERT INTO user (username, email, hash, first_name, last_name, acct_type, verify) VALUES (:username, :email, :hash, :first_name, :last_name, :acct_type, :verify_string)");
		$stmt->execute(array(
			':username' => $_POST['username'],
			':email' => $_POST['email'],
			':hash' => $hash,
			':first_name' => $_POST['first_name'],
			':last_name' => $_POST['last_name'],
			':acct_type' => $acct_type,
            ':verify_string' => $verify_string
		));
		$_SESSION['username'] = $_POST['username'];
		$_SESSION['first_name'] = $_POST['first_name'];
		$_SESSION['loggedin'] = true;

        $to = $_POST['email'];
        $msg = "Thank you for joining UNC Events Online.";
        $subject = "Email Verification (uncevents.online)";
        $ms.="Dear " .  $_POST['first_name'] . ",\n\n";
        $ms.="Please click the following link to verify and activate your account.\n
       https://uncoevents.online/verify.php?verify=$verify_string";
				emailNotifaction($ms, $subject, $to, $noreply_email_addr);
        // mail($to,$subject,$ms,$headers); // DEPRECATED

		echo '<p class="success">An email has been sent to ' . $_POST['email'] . '. Please click the link in the email to finish registering your account.</p>';
		header("Location: dash.php");
	}
	else
	{
		foreach ($errors as $error)
		{
			echo '<p class="error">'.$error.'</p>';
		}
	}
}
?>
<form action="register.php" method="POST">
 <div class="container">
   <body style="background-color: #eee;">
   <h1 style="text-align:center;">Register</h1>
   <p>Please fill in this form to create an account.</p>
   <hr>
   <b>First Name: </b><br>
   <input type="text" placeholder="First Name" name="first_name" required><br>

   <b>Last Name: </b><br>
   <input type="text" placeholder="Last Name" name="last_name" required><br>

   <b>Username: </b><br>
   <input type="text" placeholder="Username" name="username" required><br>

   <b>Email: </b><br>
   <input type="email" placeholder="Email" name="email" required><br>

   <b>Password: </b><br>
   <input type="password" placeholder="Password" name="passwd" minlength="8" required><br>

   <b>Repeat Password: </b><br>
   <input type="password" placeholder="Repeat Password" name="verify" required><br>
   <hr>
   <input type="checkbox" name="is_faculty"> I am Faculty <br>
   <button type="submit">Register</button>
 </div>
</form>
<?php require('layout/footer.php') ?>
