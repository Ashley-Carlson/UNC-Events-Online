
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
	<!-- Bootstrap core CSS -->
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
		<!-- Navigation -->
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
			<div class="container">
			<a class="navbar-brand" href="#">Start Bootstrap</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarResponsive">
				<ul class="navbar-nav ml-auto">
				<li class="nav-item active">
					<a class="nav-link" href="#">Home
					<span class="sr-only">(current)</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/">Home</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/dash.php">Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="https://www.unco.edu/research/office-of-sponsored-programs/policies-procedures-and-forms/">Policies</a>
				</li>
				</ul>
			</div>
			</div>
		</nav>
	</header>
</body>