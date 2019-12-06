
<?php
//Login Script
if(isset($_POST['submit'])){
	if (!isset($_POST['username'])) $error[] = "Please fill out all fields";
	if (!isset($_POST['password'])) $error[] = "Please fill out all fields";
	$username = $_POST['username'];
	$stmt = $db -> prepare('SELECT is_inactive FROM user WHERE username = :username');
	$stmt -> execute(array(
	  ':username' => $username,
	));
	$row = $stmt -> fetch(PDO::FETCH_ASSOC);
	if (!$row)
	{
		$error[] = 'Invalid credentials';
	}
	else if ($row["is_inactive"] != 0)
	{
		$error[] = "Account must be activated before logging in.";
	}
	else if ( $user->isValidUsername($username)){
		if (!isset($_POST['password'])){
			$error[] = 'A password must be entered';
		}
		$password = $_POST['password'];
		if($user->login($username,$password)){
			$_SESSION['username'] = $username;
			header('Location: dash.php');
			exit;
		} else {
			$error[] = 'Invalid credentials';
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
	<link rel="stylesheet" href="layout\style.css">
	<link href="layout\bootstrap\css\bootstrap.min.css" rel="stylesheet">
	<!-- Calendar mess for Firefox support -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
	<script src="https://npmcdn.com/flatpickr/dist/flatpickr.min.js"></script>
	<script src="https://npmcdn.com/flatpickr/dist/l10n/de.js"></script>
	<!-- Custom JS for site -->
	<style>
	.nav-item {
		color: white !important;
	}
	</style>
	<script>

	// Flat Picker for Datetimes
	document.addEventListener('DOMContentLoaded', function()
	{
			flatpickr('input[type="datetime-local"]', {
					enableTime: true,
					altInput: true,
					altFormat: 'm/d/Y h:i K',
					dateFormat: 'Y-m-dTH:i:S',
					locale: 'en',
					time_24hr: false,
					minDate: "today",
			});
	});
	</script>
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
							if ($user->is_logged_in())
							{
								$first_name = $_SESSION['first_name'];
								echo '<li class="nav-item"><p class="nav-par">Hello, ' . $first_name . '</p></li>
								<li class="nav-item"><a class="nav-link" href="dash.php">Profile</a></li>
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
							<a class="nav-link" href="policies.php">Policies</a>
						</li>
						<li class="nav-item">
							<form action="search.php" method="get">
								<input type="text" name="keyword" placeholder="Keyword">
								<select name="type">
									<option value="event">Event</option>
									<option value="club">Club</option>
								</select>
								<input type="submit" value="Search">
							</form>
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
<?php
if(isset($error))
{
	foreach($error as $error)
	{
		echo '<li class="error">'.$error.'</p>';
	}
}
?>
