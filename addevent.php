<?php
require_once("includes/config.php");
// assigns the "add new item" string to the $title variable
$title = 'Add New Event';
// put the header into the page
require("layout/header.php");
require("maps.php");
// if the user is logged in, put their name in the header
if (!$user->is_logged_in()) {
	header("Location: index.php");
}

$stmt = $db->prepare('SELECT user_id FROM user where username = :username');
$stmt->execute(array(':username' => $_SESSION['username']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$userID = $row['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST))
{
  $has_food = isset($_POST['has_food']);
  $event_date = date("Y-m-d H:i:s",strtotime($_POST['event_time']));

  $latLong = fnGeocode($_POST['location']);
  $stmt = $db->prepare('
	INSERT INTO event (event_name, event_desc, event_time,
	                   duration, latitude, longitude, location, has_food, external_url1,
										 external_url2, external_url3, event_contact)
	     VALUES (:name, :description, :event_time, :duration, :latitude, :longitude,
			         :location, :has_food, :external_url1,
							 :external_url2, :external_url3, :event_contact)');

  $stmt -> execute(array(
    ':name' => $_POST['name'],
    ':description' => $_POST['description'],
    ':event_time' => $event_date,
    ':duration' => $_POST['duration'],
    ':latitude' => $latLong[0],
    ':longitude' => $latLong[1],
    ':location' => $_POST['location'],
    ':has_food' => $has_food,
    ':external_url1' => $_POST['external_url1'],
		':external_url2' => $_POST['external_url2'],
		':external_url3' => $_POST['external_url3'],
    ':event_contact' => $userID,
  ));

	$eventID = $db->lastInsertId();

	if ($_POST['tags'])
	{
		$stmt = $db->prepare('INSERT INTO eventtag (event_id, tag_id) VALUES (:event_id, :tag_id)');
		foreach($_POST['tags'] as $tag_id)
		{
			$stmt->execute(array(':event_id' => $eventID, ':tag_id' => $tag_id));
		}
	}
	if (empty($_FILES['image'])
	 || !file_exists($_FILES['image']['tmp_name'])
	 || !is_uploaded_file($_FILES['image']['tmp_name'])
	)
	{
		echo "<p>No file uploaded, skipping.</p>";
	}
	else
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
		mkdir("media/events/" . $eventID, 0777, true);
		$directory = "media/events/" . $eventID . "/";
		$target = $directory . sha1_file($_FILES['image']['tmp_name']) . '.' . $ext;
		$filename = sha1_file($_FILES['image']['tmp_name']) . '.' . $ext;
		move_uploaded_file($_FILES['image']['tmp_name'], $target);

		$stmt = $db->prepare("UPDATE event SET photo_path = :photo WHERE event_id = :event_id");
		$stmt->execute(array(':photo' => $target, ':event_id' => $eventID));

		echo '<p class="success">File uploaded.</p>';
	}
	if (isset($_POST['club_id']) && $_POST['club_id'] != "none")
	{
		$stmt = $db->prepare("INSERT INTO clubevent (club_id, event_id) VALUES (:club_id, :event_id)");
		$stmt->execute(array(':club_id' => $_POST['club_id'], ':event_id' => $eventID));
	}
	// ADD AUTO-EMAIL HERE
	$stmt = $db -> prepare (
	"SELECT user.email as email, event.event_name
	 FROM user
	LEFT JOIN eventfollower ON eventfollower.user_id = user.user_id
	LEFT JOIN event ON event.event_id = eventfollower.event_id
	WHERE event.event_id = :id"
	);
	$stmt -> execute(array(":id" => $_POST["id"])); // Assuming that it posts to self with ID as a parameter
	while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) // Get associative array
	{
		$email = $row['email'];
		$event = $row['event_name'];
		$message = 'An event has been updated
		Chech out the changes at https://uncevents.online/event.php?id='.$_POST['id'];
		emailNotifaction($message, $event, $email, $noreply_email_addr);
	}
  echo '<p class="success">Event created.</p>';

	header("Location: event.php?id=".$eventID);
}
?>

<script type="text/javascript">
// Chosen filtering
$(function() {
	$(".chosen-select").chosen();
});
</script>

<!-- <body style="background-image:url('media/addeventbkg.jpg');"> -->

<form enctype="multipart/form-data" role="form" id="addevent" action="addevent.php" method="POST">
	<div class="container">
		<body class="addevent">
			<h1 class="title">Add an Event</h1>
			<hr>
			<!-- <div class="card"> -->
			<!-- takes text input for title, description, reserve -->
			<b>Event Name: </b>
			<input id="event_name_box" type="text" name="name" placeholder="Event Name" required><br>

			<br><b>Club: </b>
			<select name="club_id">
				<option value="none"></option>
				<?php
				$stmt = $db->prepare(
				"SELECT
					club.club_name,
					club.club_id
				  FROM club
				  LEFT JOIN clubmember ON club.club_id = clubmember.club_id
				 WHERE clubmember.user_id = :user_id
				   AND clubmember.is_contact = 1;");
				$stmt->execute(array(':user_id' => $userID));
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
				{
					echo '<option value="' . $row['club_id'] . '">' . $row['club_name'] . '</option>';
				}
				?>
			</select><br>

			<b>Date and Time:</b>
			<input type="datetime-local" name="event_time" required><br>

			<b>Duration:</b>
			<input type="text" name="duration" placeholder="e.g. 2 hours" required><br>

			<b>Description:</b><br>
			<textarea id="description_box" name="description" placeholder="Write something.." style="width:30%;height:20%;color:#000000" required></textarea><br>

			<!-- TO BE IMPLEMENTED -->
			<!-- <h3>Club:<br>
			<input type="text" name="description" placeholder="Description">
			</h3> -->

			<b>Address:</b>
			<input id="address_box" type="text" name="location" placeholder="Location" required><br>

			<b>External URL(s):</b><br>
			<input id="url_box" type="text" name="external_url1" placeholder="Link"><br>
			<input id="url_box" type="text" name="external_url2" placeholder="Link"><br>
			<input id="url_box" type="text" name="external_url3" placeholder="Link"><br>

			<input type="checkbox" name="has_food"><b> Does this event have food?</b><br>

			<!-- dropdown menu to assign it a tag (for searching) -->
			<b>Tags:</b><br>
				<select data-placeholder="Begin typing to filter tags..." multiple class="chosen-select" name="tags[]">
					<option value=""></option>
					<?php
					$stmt = $db->prepare("SELECT tag_id, tag FROM tag ORDER BY tag_id");
					$stmt->execute();
					while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
					{
						echo '<option value="' . $row['tag_id'] . '">' . $row['tag'] . '</option>';
					}
					?>
				</select>
			<br>

			<!-- actual file upload for the item itself -->
			<b>Upload Image:</b><br>
			<input type="file" accept=".jpg, .png, .jpeg, .bmp, .tif, .tiff|image/*" name="image" id="image">
			<br>
			<input type="checkbox" required><b> I agree that my event abides by the
				<a href="https://www.unco.edu/clubs-organizations/pdf/RSO-Manual.pdf">policy manual</a> and
				<a href="https://www.unco.edu/clubs-organizations/pdf/2018-2019-rso-constitution-guide.pdf">constitutional guidelines</a>, 
				and will submit an <a href="https://www.unco.edu/clubs-organizations/pdf/archiving-rso-records.pdf">archives request</a> (if necessary) for this event.
</b><br>

			<!-- submits the data entered to the server -->
			<input type="submit" value="Submit" id="popUpYes" >
		</body>
	</div>
</form>

<!-- For tag filtering -->
<script src="layout/jquery/jquery.min.js"></script>
<script src="layout/chosen/chosen.jquery.min.js"></script>
<link href="layout/chosen/chosen.min.css" rel="stylesheet"/>

<?php require('layout/footer.php') ?>
