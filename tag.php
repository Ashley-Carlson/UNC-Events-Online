<?php require_once('includes/config.php');
	// Fetch event
  $currentID = $_GET["id"];
	$stmt = $db->prepare("
   SELECT
	   tag.tag_id,
		 tag.tag
	 FROM tag
	 WHERE tag.tag_id = :id
  ");
	$stmt->execute(array(':id' => $currentID));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$item = array(
    "tag" => $row["tag"],
		"id" => $row["tag_id"]
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
	// Get notification status
	$notif_button_text = "";
	$stmt = $db->prepare("SELECT * FROM tagfollower WHERE user_id = :user_id AND tag_id = :tag_id");
	$stmt->execute(array(':user_id' => $userID, ':tag_id' => $_GET['id']));
	if ($stmt->rowCount() > 0)
	{
		$notif_button_text = "Unfollow";
	}
	else
	{
		$notif_button_text = "Follow";
	}
	$title = $item['tag'];
	require('layout/header.php');
?>
  <br><br>
    <div class="card">
      <h1 class="name"><?php echo $item['tag'] ?></h1>
			<br /><br />
			<?php
			if ($user->is_logged_in())
			{
				echo '
			<form action="follow.php" method="post">
				<input type="hidden" name="tag_id" value="' . $currentID . '">
				<input type="submit" value="' . $notif_button_text . '">
			</form>';
		  } ?>
    </div>
<?php require('layout/footer.php') ?>
