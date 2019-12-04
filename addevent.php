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

  $has_food = isset($_POST['has_food']);
  $event_date = date("Y-m-d H:i:s",strtotime($_POST['event_time']));

  $stmt = $db->prepare('
	INSERT INTO event (event_name, event_desc, event_time,
	                   duration, location, has_food, external_url1,
										 external_url2, external_url3, event_contact)
	     VALUES (:name, :description, :event_time, :duration,
			         :location, :has_food, :external_url1,
							 :external_url2, :external_url3, :event_contact)');

  $stmt -> execute(array(
    ':name' => $_POST['name'],
    ':description' => $_POST['description'],
    ':event_time' => $event_date,
		':duration' => $_POST['duration'],
    ':location' => $_POST['location'],
    ':has_food' => $has_food,
    ':external_url1' => $_POST['external_url1'],
		':external_url2' => $_POST['external_url2'],
		':external_url3' => $_POST['external_url3'],
    ':event_contact' => $userID,
  ));

	$stmt = $db->prepare('SELECT MAX(event_id) as m FROM event');
	$stmt -> execute();
	$row = $stmt -> fetch(PDO::FETCH_ASSOC);

	if ($_POST['tags'])
	{
		$stmt = $db->prepare('INSERT INTO eventtag (event_id, tag_id) VALUES (:event_id, :tag_id)');
		foreach($_POST['tags'] as $tag_id)
		{
			$stmt->execute(array(':event_id' => $row['m'], ':tag_id' => $tag_id));
		}
	}
	// ADD AUTO-EMAIL HERE
	$stmt = $db -> prepare (
	"SELECT user.email as email, event.event_name
	 FROM user
	LEFT JOIN eventfollower ON eventfollower.user_id = user.user_id
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
	header("Location: event.php?id=".$row['m']);
}
?>

<script type="text/javascript">
// Chosen filtering
$(function() {
	$(".chosen-select").chosen();
});
</script>

<body style="background-image:url('media/addeventbkg.jpg');">

<form action="addevent.php" method="POST">
	<h1 style="text-align:center;">Add an Event</h1>
	<div class="card">
<!-- takes text input for title, description, reserve -->
	<font color="black">
	<h3>Event Name<br>
		<input type="text" name="name" placeholder="Event Name" required>
	</h3>

	<h3>Date and Time</h3><input type="datetime-local" name="event_time" required><br>
	<h3>Duration</h3><input type="text" name="duration" required><br>

	<h3>Description</h3>
	<textarea id="subject" name="description" placeholder="Write something.." style="width:30%;height:20%;color:#000000" required></textarea>

	<!-- TO BE IMPLEMENTED -->
	<!-- <h3>Club:<br>
	<input type="text" name="description" placeholder="Description">
	</h3> -->

	<h3>Address<br>
	<input type="text" name="location" placeholder="Location" required>
	</h3>

	<h3>External URL<br>
	<input type="text" name="external_url1" placeholder="Link">
</h3><br>
<input type="text" name="external_url2" placeholder="Link">
</h3><br>
<input type="text" name="external_url3" placeholder="Link">
</h3><br>
	<p>Does this event have food? <input type="checkbox" name="has_food">
	</p><br>
	<!-- dropdown menu to assign it a tag (for searching) -->
  Tags:<br>
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
	  <br><br>

<!-- actual file upload for the item itself -->
	  Upload Image:
	  <input type="file" name="image" id="image">
		<br>
	<p>Do you agree to follow all club and event policies as defined by the UNC Office of Student Organizations:<input type="checkbox" required>
	</p>

<br /><br /><br /><br /><br />
</font>
</div>

	<!-- submits the data entered to the server -->
	 <input type="submit" value="Submit" id="popUpYes" >


</form>

<!-- For tag filtering -->
<script src="layout/jquery/jquery.min.js"></script>
<script src="layout/chosen/chosen.jquery.min.js"></script>
<link href="layout/chosen/chosen.min.css" rel="stylesheet"/>

<?php require('layout/footer.php') ?>
