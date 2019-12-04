<?php require("includes/config.php");

$id = $_POST['id2'];

if (!$user->is_logged_in()) {
	header("Location: index.php");
}

$stmt = $db -> prepare('SELECT event_name, event_contact, event_desc, location, event_time, external_url, has_food FROM event WHERE event_id = :id');
$stmt -> execute(array(
  ':id' => $id,
));

$event = $stmt -> fetch(PDO::FETCH_ASSOC);

$contact_id = $event['event_contact'];

$stmt = $db -> prepare('SELECT user_id, acct_type FROM user WHERE username = :username');
$stmt -> execute(array(
  ':username' => $_SESSION['username'],
));

$row = $stmt -> fetch(PDO::FETCH_ASSOC);

if ($contact_id != $row['user_id'] && $row['acct_type'] != 2)
{
  header("Location: index.php");
}

if (isset($_POST['id']))
{
  $stmt = $db ->prepare('INSERT INTO club (club_name, club_desc, fac_sponsor_id,
  photo_path) VALUES (:name, :description,
  :sponsor_id, :photo_url)');
  $stmt -> execute(array(
  ':name' => $_POST['name'],
	':description' => $_POST['description'],
	':sponsor_id' => $sponsor_id,
	':photo_url' => $_POST['photo_url'],
  ));
// ADD AUTO-EMAIL HERE
// ADD AUTO-EMAIL HERE
	$stmt = $db -> prepare (
	"SELECT user.email as email, event.event_name
	 FROM eventuser
	LEFT JOIN event ON event.event_id = eventuser.event_id
	LEFT JOIN user ON user.user_id = eventuser.user_id
	WHERE eventuser.event_id = :id"
	);
	$stmt -> execute(array(":id" => $_POST["id"])); // Assuming that it posts to self with ID as a parameter
	while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) // Get associative array
	{
		$email = $row['email'];
		$event = $row['event_name'];
		$message = 'An event has been updated';

		emailNotifaction($message, $event, $email, $noreply_email_addr);
	}
  header("Location: event.php?id=".$_POST['id']);
}

require('layout/header.php');

?>

<body style="background-image:url('media/addeventbkg.jpg');background-color: #333;">

<form action="editevent.php" method="POST">
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

		<h3>Photo:<br>
		<input type="file" name="image" id="image">
 		<br>

		<p>Do you agree to follow all club and event policies as defined by the UNC Office of Student Organizations:<input type="checkbox" required>
		</p>
		</font>
		<!-- submits the data entered to the server -->
		 <input type="submit" value="Submit" id="popUpYes" color: white >
		</div>
	</form>


	<?php require('layout/footer.php') ?>
