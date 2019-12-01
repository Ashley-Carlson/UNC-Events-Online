
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
	<link href="layout\bootstrap\css\bootstrap.min.css" rel="stylesheet">
	<!-- <link rel="stylesheet" href="layout/index.css"> -->
	<title><?php if(isset($title)){ echo $title; }?></title>
</head>
<body>
	<header>
		<!-- Navigation -->
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
			<div class="container">
				<a class="navbar-brand" href="/">UNC Events</a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarResponsive">
					<ul class="navbar-nav ml-auto2">
						<?php
							if(isset($error))
							{
								foreach($error as $error)
								{
									echo '<li class="nav-item">'.$error.'</p>';
								}
							} 
							if ($user->is_logged_in())
							{
								$first_name = $_SESSION['first_name'];
								echo '<li class="nav-item"><p class="nav-par">Hello, ' . $first_name . '</p></li>
								<li class="nav-item"><a class="nav-link" href="dash.php">Dashboard</a></li>
								<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>';
							}
							else 
							{
								echo '<li class="nav-item"><p class="nav-par">Hello, Anon</p></li>
								<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
								<li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>';
							}
						?>
					</ul>
					<ul class="navbar-nav ml-auto">
						<li class="nav-item">
							<a class="nav-link" href="addevent.php">New Event</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="/">Home</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="https://www.unco.edu/research/office-of-sponsored-programs/policies-procedures-and-forms/">Policies</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>

		<!-- Bootstrap core JavaScript -->
		<script src="layout/jquery/jquery.slim.min.js"></script>
		<script src="layout/bootstrap/js/bootstrap.bundle.min.js"></script>
	</header>
</body>