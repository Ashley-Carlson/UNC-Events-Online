<?php require("includes/config.php");

$id = $_POST['id2'];

if (!$user->is_logged_in()) {
	header("Location: index.php");
}

$stmt = $db -> prepare("
SELECT
  event.event_name,
	event.event_desc,
	event.location,
	event.event_time,
	event.external_url1,
	event.external_url2,
	event.external_url3,
	event.has_food,
	event.event_contact,
	CONCAT(user.first_name, ' ', user.last_name)
FROM event
LEFT JOIN user ON event.event_contact = user.user_id
WHERE event_id = :id"
);
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
  $stmt = $db -> prepare(
    "UPDATE event
        SET
				  event_name = :name,
					event_time = :event_time,
          event_desc = :description,
					location = :location,
					duration = :duration,
          external_url1 = :external_url1,
					external_url2 = :external_url2
					external_url3 = :external_url3,
					has_food = :has_food
      WHERE event_id = :id"
  );
  $has_food = (isset($_POST['has_food']) && $_POST['has_food'] == 'on') ? 1 : 0;

  $time = date("Y-m-d H:i:s",strtotime($_POST['event_time']));

  $stmt -> execute(array(
    ':name'         => $_POST['name'],
    ':event_time'   => $time,
    ':description'  => $_POST['description'],
    ':location'     => $_POST['location'],
    ':external_url1' => $_POST['external_url1'],
		':external_url2' => $_POST['external_url2'],
		':external_url3' => $_POST['external_url3'],
    ':has_food'     => $has_food,
		':id'						=> $_POST['id'],
		':duration' => $_POST['duration']
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
		mkdir("media/events/" . $id, 0777, true);
		$directory = "media/events/" . $id . "/";
		$target = $directory . sha1_file($_FILES['image']['tmp_name']) . '.' . $ext;
		$filename = sha1_file($_FILES['image']['tmp_name']) . '.' . $ext;
		move_uploaded_file($_FILES['image']['tmp_name'], $target);

		$stmt = $db->prepare("UPDATE event SET photo_path = :photo WHERE event_id = :event_id");
		$stmt->execute(array(':photo' => $target, ':event_id' => $id));

		echo '<p class="success">File uploaded.</p>';
	}
// ADD AUTO-EMAIL HERE
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
	if ($_POST['tags'])
	{
		foreach($_POST['tags'] as $tag_id)
		{
			$deletecurrent = $db->prepare('DELETE FROM eventtag WHERE event_id = :id');
			$deletecurrent->execute(array(":id" => $id));
			$insertstmt = $db->prepare('INSERT INTO eventtag (event_id, tag_id) VALUES (:event_id, :tag_id)');
			$insertstmt->execute(array(':event_id' => $row['m'], ':tag_id' => $tag_id));
		}
	}
  header("Location: event.php?id=".$_POST['id']);
}

require('layout/header.php');

?>

<script type="text/javascript">
// Chosen filtering
$(function() {
	$(".chosen-select").chosen();
});
</script>

	<form enctype="multipart/form-data" role="form" id="addevent" action="addevent.php" method="POST">
		<div class="container">
			<body class="addevent">
				<h1 class="title">Add an Event</h1>
				<hr>
	<!-- takes text input for title, description, reserve -->
		<font color="black">
			<b>Event Name: </b>
			<input id="event_name_box" type="text" name="name" placeholder="Event Name" required><br>

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

		<p>Does this event have food? (Check if yes)<input type="checkbox" name="has_food" <?php if ($event['has_food'] == 1) { echo 'checked="checked"'; } ?>>
		</p><br>
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
		<input type="checkbox" required><b> I agree that my event abides by the following the
			<a href="https://www.unco.edu/clubs-organizations/pdf/RSO-Manual.pdf">policy manual</a>,
			<a href="https://www.unco.edu/clubs-organizations/pdf/2018-2019-rso-constitution-guide.pdf">constitutional guidelines </a>
			and will submit an <a href="https://www.unco.edu/clubs-organizations/pdf/archiving-rso-records.pdf">archives request</a> (if necessary) for this event.
</b><br>
		</font>
		<br><br><br>
		<div>
		<!-- submits the data entered to the server -->
		 <input type="submit" value="Submit" id="popUpYes">
	   <input type="hidden" value=<?php echo $id ?> name="id">
</form>

<!-- For tag filtering -->
<script src="layout/jquery/jquery.min.js"></script>
<script src="layout/chosen/chosen.jquery.min.js"></script>
<script>

$('#documents').val(<?php
$stmt = $db->prepare(
	"SELECT tag.tag_id
FROM tag
LEFT JOIN eventtag ON eventtag.tag_id = tag.tag_id
LEFT JOIN event ON event.event_id = eventtag.event_id
WHERE event.event_id = :id"
);
$stmt->execute(array(":id" => $id));
$tagstring = "[";
while ($stmt->fetch(PDO::FETCH_ASSOC))
{
	$tagstring .= '"'.$row['tag_id'].'", ';
}
$tagstring .= "]";
echo $tagstring;
?>).trigger('chosen:updated');

</script>
<link href="layout/chosen/chosen.min.css" rel="stylesheet"/>

<?php require('layout/footer.php') ?>
