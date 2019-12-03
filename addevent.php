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
  $stmt = $db->prepare('INSERT INTO event (event_name, event_desc, event_time, location, has_food, external_url, event_contact) VALUES (:name, :description, :event_time, :location, :has_food, :external_url, :event_contact)');
  $stmt -> execute(array(
    ':name' => $_POST['name'],
    ':description' => $_POST['description'],
    ':event_time' => $event_date,
    ':location' => $_POST['location'],
    ':has_food' => $has_food,
    ':external_url1' => $_POST['external_url1'],
    ':event_contact' => $userID,
  ));

  echo '<p class="success">Event created.</p>';

	$stmt = $db->prepare('SELECT MAX(event_id) as m FROM event');
	$stmt -> execute();
	$row = $stmt -> fetch(PDO::FETCH_ASSOC);
	header("Location: event.php?id=".$row['m']);
}
?>

<body style="background-image:url('media/addeventbkg.jpg');">

<form action="addevent.php" method="POST">
	<h1 style="text-align:center;">Add an Event</h1>
	<div class="card">
<!-- takes text input for title, description, reserve -->
	<font color="black">
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
	<input type="text" name="external_url1">
	</h3><br>
	<h3>External URL<br>
	<input type="text" name="external_url2">
	</h3><br>
	<h3>External URL<br>
	<input type="text" name="external_url3">
	</h3><br>
	<p>Does this event have food? <input type="checkbox" name="has_food">
	</p><br>
	<!-- dropdown menu to assign it a tag (for searching) -->
	  Tags:<br>
		<select>
			<option value="biology">Biology</option>
			<option value="mathematics">Mathematics</option>
			<option value="technology">Technology</option>
			<option value="art">Art</option>
			<option value="science">Science</option>
			<option value="performance">Performance</option>
			<option value="theater">Theater</option>
			<option value="chemistry">Chemistry</option>
			<option value="culture">Culture</option>
			<option value="cuisine">Cuisine</option>
			<option value="animals">Animals</option>
			<option value="21">21+</option>
			<option value="official">Official</option>
			<option value="party">Party</option>
			<option value="greek">Greek</option>
			<option value="mechanics">Mechanics</option>
			<option value="engineering">Engineering</option>
			<option value="ai">AI</option>
			<option value="business">Business</option>
			<option value="networking">Networking</option>
			<option value="food">Food</option>
			<option value="sports">Sports</option>
			<option value="football">Football</option>
			<option value="volleyball">Volleyball</option>
			<option value="soccer">Soccer</option>
		</select>
	  <br><br>

<!-- actual file upload for the item itself -->

	  Upload Image:
	  <input type="file" name="image" id="image">
		<br>
	<p>Do you agree to follow all club and event policies as defined by the UNC Office of Student Organizations:<input type="checkbox" required>
	</p>
	</font>

<br /><br /><br /><br /><br />
	<!-- submits the data entered to the server -->
	 <button type="submit" value="Submit" id="popUpYes" color: white >

 </div>

</form>

<?php require('layout/footer.php') ?>
