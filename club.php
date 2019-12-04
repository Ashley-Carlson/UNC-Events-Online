<?php require_once('includes/config.php');
  $currentID = $_GET["id"];
	$stmt = $db->prepare("
    SELECT
	    club.club_name,
		  club.club_desc,
			club.photo_path,
			user.user_id,
      user.email,
		  CONCAT(user.first_name, ' ', user.last_name) as name
     FROM club
LEFT JOIN user ON user.user_id = club.fac_sponsor_id
    WHERE club_id = :id
  ");
	$stmt->execute(array(':id' => $currentID));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$item = array(
    'name' => $row['club_name'],
    'description' => $row['club_desc'],
    'sponsor' => $row['name'],
		'sponsor_id' => $row['user_id'],
		'email' => $row['email'],
    'photo' => $row['photo_path']
  );
  $stmt = $db -> prepare("
  SELECT user_id
    FROM user
   WHERE username = :username
  ");
  $stmt -> execute(array(':username' => $_SESSION['username']));
  $row = $stmt -> fetch(PDO::FETCH_ASSOC);
  $userID = $row['user_id'];
	// Get notification status
	$notif_button_text = "";
	$stmt = $db->prepare("SELECT * FROM clubfollower WHERE user_id = :user_id AND club_id = :club_id");
	$stmt->execute(array(':user_id' => $userID, ':club_id' => $_GET['id']));
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
   <div class="container">
     <h1 style="color: #eacc1f; text-align:center;"><?php echo $item['name'] ?></h1>
    </div>
     <div class="card" style="color: White;">
     <body style="background-color: #333;">
     <h3 style="color: black; text-align:center;"><?php echo $item['description'] ?></h3>
     </div>
     <div class="card" style="color: White;">
       <!--<h2>Faculty Sponsor: </h2>-->
       <h2  style="color: #000080;">Contact Name: <?php echo $item['sponsor'] ?></h2>
       <p style="color: #000080;">Contact email: <?php echo $item['email'] ?></p>
     </div>
		 <br /><br />
     <div>
			 <?php
 			if ($user->is_logged_in())
 			{
 				echo '
 			<form action="follow.php" method="post">
 				<input type="hidden" name="club_id" value="' . $currentID . '">
 				<input type="submit" value="' . $notif_button_text . '">
 			</form>';
 		  } ?>
     </div>
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
   } ?>

<?php require('layout/footer.php') ?>
