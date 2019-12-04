<?php require_once('includes/config.php');
	$stmt = $db->prepare("SELECT user_id FROM user WHERE username = :username");
	$stmt->execute(array(':username' => $_SESSION['username']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$user_id = $row['user_id'];
  if (isset($_POST['event_id']))
	{
		$stmt = $db->prepare("SELECT * FROM eventfollower WHERE user_id = :user_id AND event_id = :event_id");
		$stmt->execute(array(':user_id' => $user_id, ':event_id' => $_POST['event_id']));
		if ($stmt->rowCount() > 0)
		{
			$stmt = $db->prepare("DELETE FROM eventfollower WHERE user_id = :user_id AND event_id = :event_id");
			$stmt->execute(array(':user_id' => $user_id, ':event_id' => $_POST['event_id']));
		}
		else
		{
			$stmt = $db->prepare("INSERT INTO eventfollower (user_id, event_id) VALUES (:user_id, :event_id)");
			$stmt->execute(array(':user_id' => $user_id, ':event_id' => $_POST['event_id']));
		}
		header("Location: event.php?id=" . $_POST['event_id']);
	}
	if (isset($_POST['club_id']))
	{
		$stmt = $db->prepare("SELECT * FROM clubfollower WHERE user_id = :user_id AND club_id = :club_id");
		$stmt->execute(array(':user_id' => $user_id, ':club_id' => $_POST['club_id']));
		if ($stmt->rowCount() > 0)
		{
			$stmt = $db->prepare("DELETE FROM clubfollower WHERE user_id = :user_id AND club_id = :club_id");
			$stmt->execute(array(':user_id' => $user_id, ':club_id' => $_POST['club_id']));
		}
		else
		{
			$stmt = $db->prepare("INSERT INTO clubfollower (user_id, club_id) VALUES (:user_id, :club_id)");
			$stmt->execute(array(':user_id' => $user_id, ':club_id' => $_POST['club_id']));
		}
		header("Location: club.php?id=" . $_POST['club_id']);
	}
	else if (isset($_POST['tag_id']))
	{
		$stmt = $db->prepare("SELECT * FROM tagfollower WHERE user_id = :user_id AND tag_id = :tag_id");
		$stmt->execute(array(':user_id' => $user_id, ':tag_id' => $_POST['tag_id']));
		if ($stmt->rowCount() > 0)
		{
			$stmt = $db->prepare("DELETE FROM tagfollower WHERE user_id = :user_id AND tag_id = :tag_id");
			$stmt->execute(array(':user_id' => $user_id, ':tag_id' => $_POST['tag_id']));
		}
		else
		{
			$stmt = $db->prepare("INSERT INTO tagfollower (user_id, tag_id) VALUES (:user_id, :tag_id)");
			$stmt->execute(array(':user_id' => $user_id, ':tag_id' => $_POST['tag_id']));
		}
		header("Location: tag.php?id=" . $_POST['tag_id']);
	}
 ?>
