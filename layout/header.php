
<?php
//Login Script
if(isset($_POST['submit'])){
	if (!isset($_POST['username'])) $error[] = "Please fill out all fields";
	if (!isset($_POST['password'])) $error[] = "Please fill out all fields";
	$username = $_POST['username'];
	if ( $user->isValidUsername($username)){
		if (!isset($_POST['password'])){
			$error[] = 'A password must be entered';
		}
		$password = $_POST['password'];
		if($user->login($username,$password)){
			$_SESSION['username'] = $username;
			header('Location: dash.php');
			exit;
		} else {
			$error[] = 'Wrong username or password.';
		}
	}else{
		$error[] = 'Usernames are required to be Alphanumeric, and between 3-16 characters long';
	}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<meta name="viewport" content="width-device-width, initial-scale=1"/>
	<link rel="stylesheet" href="layout/index.css">
	<title><?php if(isset($title)){ echo $title; }?></title>
</head>
<body>
	<header>
		<?php
			if(isset($error))
			{
				foreach($error as $error)
				{
					echo '<p class="error">'.$error.'</p>';
				}
			} 
			if ($user->is_logged_in())
			{
				$first_name = $_SESSION['first_name'];
				echo '<p>Hello, ' . $first_name . '</p>
				<div id="submitButton">
					<button type="button"><a href="dash.php">Dashboard</a></button>
					<button type="button"><a href="logout.php">Logout</a></button>
				</div>';
			}
			else 
			{
				echo '<p>Hello, Anon</p>
				<div id="submitButton">
					<button type="button"><a href="login.php">Login</a></button>
					<button type="button"><a href="register.php">Register</a></button>
				</div>';
			}
		?>

		<div class="topnav">
			<a href="/">Home</a>
			<a href="/dash.php">Dashboard</a>
			<a href="https://www.unco.edu/research/office-of-sponsored-programs/policies-procedures-and-forms/">Policies</a>
		</div>
	</header>
</body>