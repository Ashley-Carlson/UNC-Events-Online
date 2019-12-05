
<?php require_once('includes/config.php');
  $currentID = $_GET["id"];
	$stmt = $db->prepare("
    SELECT
	    club.club_name,
		  club.club_desc,
			club.photo_path
     FROM club
    WHERE club_id = :id
  ");
//open
	$stmt->execute(array(':id' => $currentID));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$item = array(
    'name' => $row['club_name'],
    'description' => $row['club_desc'],
    'photo_path' => $row['photo_path']
  );
	$stmt = $db->prepare(
		"SELECT
		   CONCAT(user.first_name, ' ', user.last_name) as name,
			 user.email
		 FROM club
		 LEFT JOIN clubmember ON club.club_id = clubmember.club_id
		 LEFT JOIN user ON user.user_id = clubmember.user_id
		 WHERE club.club_id = :club_id
		   AND clubmember.is_contact = 1
		"
	);
  $stmt->execute(array(':club_id'=>$currentID));
	$contact_info = $stmt->fetch(PDO::FETCH_ASSOC);
  $stmt = $db -> prepare('SELECT user_id FROM user WHERE username = :username');
	$stmt->execute(array(':username' => $_SESSION['username']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
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
	$edit_stmt = $db->prepare('SELECT can_edit FROM clubmember WHERE user_id = :user_id AND club_id = :club_id');
  $edit_stmt->execute(array(':user_id' => $userID, ':club_id' => $_GET['id']));
	$title = $item['name'];
	require('layout/header.php');
?>
<?php
  while($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
     $stmt = $db->prepare("
    SELECT clubtag.tag_id as tag_id, tag.tag AS tag
      FROM clubtag
 LEFT JOIN tag ON tag.tag_id = clubtag.tag_id
     WHERE clubtag.club_id = :club_id
 ");
 ?>
  <br><br>
   <div class="card">
     <h1 class="name"><?php echo $item['name'] ?></h1>
     <body>
       <?php
       $filepath = isset($item['photo_path']) ? $item['photo_path'] : "media/logo.png";
       echo
       <img src="'. $filepath .'" alt="UNC" style="width:15%">
       ?>

     <h3 id="description"><?php echo $item['description'] ?></h3>
     </div>
     <div class="card">
       <!--<h2>Faculty Sponsor: </h2>-->
       <p>Contact Name: <?php echo $contact_info['name'] ?></p>
       <p>Contact email: <?php echo $contact_info['email'] ?></p>
     </div>
		 <br /><br />
     <div>
			 <?php
 			if ($user->is_logged_in())
 			{
 				echo '
 			<form action="follow.php" method="post">
 				<input type="hidden" name="club_id" value="' . $currentID . '">
 				<input id="notif-button" type="submit" value="' . $notif_button_text . '">
 			</form>';
 		  } ?>
     </div>
     <?php
     if ($edit_stmt->rowCount() > 0)
     {
      echo '
     <form action="editclub.php" method="post">
     <input type="hidden" name="id2" value="' . $currentID . '">
     <input type="submit" value="Edit">
     </form>
     <form action="deleteclub.php" method="post">
     <input type="hidden" name="id3" value="' . $currentID . '">
     <input type="submit" value="Delete">
     </form>';
   } ?>

<?php require('layout/footer.php') ?>
