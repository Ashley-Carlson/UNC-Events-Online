<?php require_once('includes/config.php');
    require('maps.php');
	// Fetch event
    $currentID = $_GET["id"];
    $stmt = $db->prepare("
    SELECT event.event_name as event_name,
        event.event_desc as event_desc,
        event.has_food as has_food,
        event.event_time as event_time,
        event.photo_path as photo_path,
        event.location as location,
        event.latitude as lat,
        event.longitude as lon,
        user.user_id as user_id,
        user.first_name as first_name,
        user.last_name as last_name,
        user.email as email,
        event.duration as duration



    FROM event
    LEFT JOIN user ON user.user_id = event.event_contact
    WHERE event_id = :id
    ");
    $stmt->execute(array(':id' => $currentID));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $item = array(
        'name'        => $row['event_name'],
        'description' => $row['event_desc'],
        'has_food'    => $row['has_food'],
        'location'    => $row['location'],
        'lat'         => $row['lat'],
        'lon'         => $row['lon'],
        'event_time'  => $row['event_time'],
        'duration'    => $row['duration'],
        'first_name'  => $row['first_name'],
        'last_name'   => $row['last_name'],
        'email'       => $row['email'],
        'user_id'     => $row['user_id'],
        'photo_path' => $row['photo_path'],
        'external_url1' => $row['external_url1'],
        'external_url2' => $row['external_url2'],
        'external_url3' => $row['external_url3']
    );
    $stmt = $db -> prepare("
    SELECT user_id
    FROM user
    WHERE username = :username
    ");
    // Check event ownership
    $stmt -> execute(array(':username' => $_SESSION['username']));
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    $userID = $row['user_id'];
    $phptime = strtotime($item['event_time']);
    $time = date("m/d/y g:i A", $phptime);
	// Get notification status
	$notif_button_text = "";
	$stmt = $db->prepare("SELECT * FROM eventfollower WHERE user_id = :user_id AND event_id = :event_id");
	$stmt->execute(array(':user_id' => $userID, ':event_id' => $_GET['id']));
	if ($stmt->rowCount() > 0)
	{
		$notif_button_text = "Unfollow";
	}
	else
	{
		$notif_button_text = "Follow";
	}
	$title = $item['name'];
	require('layout/header.php');
?>
  <br><br>
    <div class="card">
      <?php
      $filepath = isset($item['photo_path']) ? $item['photo_path'] : "media/logo.png";
      echo '<div class="event-card-image"><img src='. $filepath .' alt="UNC" id="event-card-image"></div>'
      ?>
      <br>
      <h1 class="name"><?php echo $item['name'] ?></h1>
      <p id="time">When: <?php echo $time ?></p>
      <p>Duration: <?php echo $item['duration'] ?></p>

      <?php if ($item['has_food'] == 1) { echo 'This event has food!<br><br>'; }?>

      <body>
      <p>Description:</p>
      <h3 id="description"><?php echo $item['description'] ?></h3>

      <p style="color: #000080;">Contact Name: <?php echo $item['first_name'].' '.$item['last_name'] ?></p>
      <p style="color: #000080;">Contact email: <?php echo $item['email'] ?></p>

      <p>Location: <?php echo $item['location'] ?></p>
      <p>More info found:</p>
      <?php if (isset($item['external_url1'])) { echo '<a href="' . $item['external_url1'] . '">Link</a>'; }?>
      <?php if (isset($item['external_url2'])) { echo '<a href="' . $item['external_url2'] . '">Link</a>'; }?>
      <?php if (isset($item['external_url3'])) { echo '<a href="' . $item['external_url3'] . '">Link</a>'; }?>
      
      <br>


      <?php
			if ($user->is_logged_in())
			{
				echo '
			<form action="follow.php" method="post">
				<input type="hidden" name="event_id" value="' . $currentID . '">
				<input type="submit" value="' . $notif_button_text . '">
			</form>';
		  } ?>

      <?php
          if ($userID == $item['user_id'])
          {
              echo '
              <form action="editevent.php" method="post">
              <input type="hidden" name="id2" value="' . $currentID . '">
              <input type="submit" value="Edit">
              </form>
              <form action="deleteevent.php" method="post">
              <input type="hidden" name="id3" value="' . $currentID . '">
              <input type="submit" value="Delete">
              </form>';
          }
      ?>
      <br>

      <?php echo renderMap($item['lat'], $item['lon']); ?>
    </div>
    <div>
</div>




<?php require('layout/footer.php') ?>
