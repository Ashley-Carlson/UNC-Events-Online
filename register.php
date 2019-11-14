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
		$stmt = $db->prepare("INSERT INTO user (username, email, hash, first_name, last_name, acct_type) VALUES (:username, :email, :hash, :first_name, :last_name, :acct_type)");
		$stmt->execute(array(
			':username' => $_POST['username'],
			':email' => $_POST['email'],
			':hash' => $hash,
			':first_name' => $_POST['first_name'],
			':last_name' => $_POST['last_name'],
			':acct_type' => $acct_type,
		));
		$_SESSION['username'] = $_POST['username'];
		$_SESSION['first_name'] = $_POST['first_name'];
		$_SESSION['loggedin'] = true;

		echo '<p class="success">Account registered successfully! You can now log in</p>';
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
   <body style="background-color: #333;">
   <h1 style="text-align:center;">Register</h1>
   <p>Please fill in this form to create an account.</p>
   <hr>
   <b>First Name: </b>
   <input type="text" placeholder="First Name" name="first_name" required>

   <b>Last Name: </b>
   <input type="text" placeholder="Last Name" name="last_name" required>
   <br>

   <b>Username: </b>
   <input type="text" placeholder="Username" name="username" required>

   <b>Email: </b>
   <input type="email" placeholder="Email" name="email" required>
   <br>

   <b>Password: </b>
   <input type="password" placeholder="Password" name="passwd" minlength="8" required>

   <b>Repeat Password: </b>
   <input type="password" placeholder="Repeat Password" name="verify" required>
   <hr>
   <br>
   <input type="checkbox" name="is_faculty"> I am Faculty <br>
   <button type="submit">Register</button>
 </div>
</form>
<?php require('layout/footer.php') ?>
