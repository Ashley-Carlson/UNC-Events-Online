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
  :sponsor_id)');
  $stmt -> execute(array(
  ':name' => $_POST['name'],
	':description' => $_POST['description'],
	':sponsor_id' => $sponsor_id
  ));
	if (isset($_FILES['image']))
	{
		if ($_FILES['image']['size'] > 1000000)
		{
			throw new RuntimeException('Exceeded filesize limit.');
		}
    $finfo = new finfo(FILEINFO_MIME_TYPE);
		if (false === $ext = array_search(
			 $finfo->file($_FILES['image']['tmp_name']),
			 array(
					 'jpg' => 'image/jpeg',
					 'png' => 'image/png',
					 'gif' => 'image/gif',
			 ),
			 true
	 )) {
			 throw new RuntimeException('Invalid file format.');
	 }
		mkdir("media/clubs/" . $id, 0777, true);
		$directory = "media/clubs/" . $id . "/";
		$target = $directory . sha1_file($_FILES['image']['tmp_name']) . '.' . $ext;
		$filename = sha1_file($_FILES['image']['tmp_name']) . '.' . $ext;
		move_uploaded_file($_FILES['image']['tmp_name'], $target);

		$stmt = $db->prepare("UPDATE club SET photo_path = :photo WHERE club_id = :club_id");
		$stmt->execute(array(':photo' => $target, ':club_id' => $id));

		echo '<p class="success">File uploaded.</p>';
	}
// ADD AUTO-EMAIL HERE
// ADD AUTO-EMAIL HERE
	$stmt = $db -> prepare (
	"SELECT user.email as email, event.event_name
	 FROM eventfollower
	LEFT JOIN event ON event.event_id = eventfollower.event_id
	LEFT JOIN user ON user.user_id = eventfollower.user_id
	WHERE eventfollower.event_id = :id"
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

<form enctype="multipart/form-data" action="editevent.php" method="POST">
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
