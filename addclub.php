<?php
require_once("includes/config.php");
// assigns the "add new item" string to the $title variable
$title = 'Create New Club';
// put the header into the page
require("layout/header.php");
// if the user is logged in, put their name in the header
if (!$user->is_logged_in()) {
	header("Location: index.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST))
{

  $stmt = $db->prepare('SELECT user_id FROM user where username = :username');
  $stmt->execute(array(':username' => $_SESSION['username']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $userID = $row['user_id'];

  $event_date = date("Y-m-d H:i:s",strtotime($_POST['event_time']));
  $stmt = $db->prepare('INSERT INTO club (club_name, club_desc, fac_sponsor_id, 
  photo_url, create_timestmp, updt_timestmp, is_inactive) VALUES (:name, :description, 
  :sponsor_id, :photo_url, :create_timestmp, :updt_timestmp, 0)');
  $stmt -> execute(array(
    ':name' => $_POST['name'],
	':description' => $_POST['description'],
	':sponsor_id' => $sponsor_id,
	':photo_url' => $_POST['photo_url'],
	':create_timestmp' => date('Y-m-d'),
	':updt_timestmp' => date('Y-m-d'),
  ));

  echo '<p class="success">Club created.</p>';

	$stmt = $db->prepare('SELECT MAX(club_id) as m FROM club');
	$stmt -> execute();
	$row = $stmt -> fetch(PDO::FETCH_ASSOC);
	header("Location: club.php?id=".$row['m']);
}
?>

<form action="addclub.php" method="POST">
	<h1 style="text-align:center;">Add a Club</h1>
	<div class="card">
<!-- takes text input for title, description, reserve -->
	<font color="black">
	<h3>Club Name<br>
		<input type="text" name="name" placeholder="Event Name">
	</h3>

	<h3>Description</h3>
	<textarea id="subject" name="description" placeholder="Write something..." style="width:30%;height:20%;color:#000000"></textarea>

	<h3>Sponsor ID<br>
	<input type="text" name="sponsor_id" placeholder="Sponsor">
	</h3>

	<h3>Photo URL<br>
	<input type="text" name="external_url" placeholder="URL">

    <!-- actual file upload for the item itself -->
    <!-- Upload Image:
	  <input type="file" name="image" id="image"> -->

	<p>Do you agree to follow all club and event policies as defined by the UNC Office of Student Organizations:<input type="checkbox" required>
	</p>
	</font>
	<!-- submits the data entered to the server -->
	 <input type="submit" value="Submit" id="popUpYes" color: white >
	</div>
</form>

<?php require('layout/footer.php') ?>
