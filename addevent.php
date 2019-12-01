<?php
require_once("includes/config.php");
// assigns the "add new item" string to the $title variable
$title = 'Add New Event';
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

  $has_food = (isset($_POST['has_food']) && $_POST['has_food'] == 'on') ? 1 : 0;
  $event_date = date("Y-m-d H:i:s",strtotime($_POST['event_time']));
  $stmt = $db->prepare('INSERT INTO event (event_name, event_desc, event_time, location, has_food, external_url, event_contact) VALUES (:name, :description, :event_time, :location, :has_food, :external_url, :event_contact)');
  $stmt -> execute(array(
    ':name' => $_POST['name'],
    ':description' => $_POST['description'],
    ':event_time' => $event_date,
    ':location' => $_POST['location'],
    ':has_food' => $has_food,
    ':external_url' => $_POST['external_url'],
    ':event_contact' => $userID,
  ));

  echo '<p class="success">Event created.</p>';

	$stmt = $db->prepare('SELECT MAX(event_id) as m FROM event');
	$stmt -> execute();
	$row = $stmt -> fetch(PDO::FETCH_ASSOC);
	header("Location: event.php?id=".$row['m']);
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://npmcdn.com/flatpickr/dist/flatpickr.min.js"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/de.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function()
{
		flatpickr('input[type="datetime-local"]', {
				enableTime: true,
				altInput: true,
				altFormat: 'm/d/Y h:i K',
				dateFormat: 'Y-m-dTH:i:S',
				locale: 'en',
				time_24hr: false
		});
});
</script>

<body style="background-image:url('media/addeventbkg.jpg');">

<form action="addevent.php" method="POST">
	<h1 style="text-align:center;">Add an Event</h1>
	<div class="card">
<!-- takes text input for title, description, reserve -->
	<font color="#ffffff">
	<h3>Event Name<br>
		<input type="text" name="name" placeholder="Event Name">
	</h3>

	<h3>Date and Time</h3><input type="datetime-local" name="event_time">


	<h3>Description</h3>
	<textarea id="subject" name="description" placeholder="Write something.." style="width:30%;height:20%;color:#000000"></textarea>

	<!-- TO BE IMPLEMENTED -->
	<!-- <h3>Club:<br>
	<input type="text" name="description" placeholder="Description">
	</h3> -->

	<h3>Address<br>
	<input type="text" name="location" placeholder="Location">
	</h3>

	<h3>External URL<br>
	<input type="text" name="external_url" placeholder="Link">
</h3><br>
	<p>There is food <input type="checkbox" name="has_food">
	</p><br>
	<p>Do you agree to follow all club and event policies as defined by the UNC Office of Student Organizations:<input type="checkbox" required>
	</p>
	</font>
	<!-- submits the data entered to the server -->
	 <input type="submit" value="Submit" id="popUpYes" color: white >
	</div>
</form>

<?php require('layout/footer.php') ?>
