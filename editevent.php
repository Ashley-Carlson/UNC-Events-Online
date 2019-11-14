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

$contact_id = $row['event_contact'];

$stmt = $db -> prepare('SELECT user_id, acct_type FROM user WHERE username = :username');
$stmt -> execute(array(
  ':username' => $_SESSION['username'],
));

$row = $stmt -> fetch(PDO::FETCH_ASSOC);

if ($contact_id != $row['user_id'] || $row['acct_type'] != 2)
{
  header("Location: index.php");
}

if (isset($_POST['id']))
{
  $stmt = $db -> prepare(
    "UPDATE event
        SET event_name = :name, event_time = :event_time,
            event_desc = :description, location = :location,
            external_url = :external_url, has_food = :has_food
      WHERE event_id = :id"
  );
  $has_food = (isset($_POST['has_food']) && $_POST['has_food'] == 'on') ? 1 : 0;

  $time = date("Y-m-d H:i:s",strtotime($_POST['event_time']));

  $stmt -> execute(array(
    ':name'         => $_POST['name'],
    ':event_time'   => $time,
    ':description'  => $_POST['description'],
    ':location'     => $_POST['location'],
    ':external_url' => $_POST['external_url'],
    ':has_food'     => $has_food,
		':id'						=> $_POST['id'],
  ));
// ADD AUTO-EMAIL HERE
  header("Location: event.php?id=".$_POST['id']);
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

<body style="background-image:url('media/addeventbkg.jpg');background-color: #333;">

<form action="editevent.php" method="POST">
	<h1 style="text-align:center;">Edit Event Event</h1>
		<div class="card">
	<!-- takes text input for title, description, reserve -->
		<font color="#ffffff">
		<h3>Event Name<br>
			<input type="text" name="name" value="<?php echo $event['event_name'] ?>">
		</h3>

		<h3>Date and Time</h3><input type="datetime-local" name="event_time" value="<?php echo date("Y-m-d\TH:i:s", strtotime($event['event_time'])) ?>">


		<h3>Description</h3>
		<textarea id="subject" name="description" style="width:30%;height:20%;color:#000000"><?php echo $event['event_desc'] ?></textarea>

		<!-- TO BE IMPLEMENTED -->
		<!-- <h3>Club:<br>
		<input type="text" name="description" placeholder="Description">
		</h3> -->

		<h3>Address<br>
		<input type="text" name="location" value="<?php echo $event['location'] ?>">
		</h3>

		<h3>External URL<br>
		<input type="text" name="external_url" value="<?php echo $event['external_url'] ?>">
		</h3><br>
		<p>There is food <input type="checkbox" name="has_food" <?php if ($event['has_food'] == 1) { echo 'checked'; } ?>>
		</p><br>
		<p>Do you agree to follow the UNC code of conduct:<input type="checkbox" required>
		</p>
		</font>
		<!-- submits the data entered to the server -->
		 <input type="submit" value="Submit" id="popUpYes">
	   <input type="hidden" value=<?php echo $id ?> name="id">
	</div>
</form>

<?php require('layout/footer.php') ?>
