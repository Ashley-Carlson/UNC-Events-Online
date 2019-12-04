<?php require_once('includes/config.php');
	$stmt = $db->prepare("SELECT user_id FROM user WHERE username = :username");
	$stmt->execute(array(':username' => $_SESSION['username']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$user_id = $row['user_id'];
  if (isset($_POST['event_id']))
	{
		$stmt = $db->prepare("SELECT * FROM eventuser WHERE user_id = :user_id AND event_id = :event_id");
		$stmt->execute(array(':user_id' => $user_id, ':event_id' => $_POST['event_id']));
		if ($stmt->rowCount() > 0)
		{
			$stmt = $db->prepare("DELETE FROM eventuser WHERE user_id = :user_id AND event_id = :event_id");
			$stmt->execute(array(':user_id' => $user_id, ':event_id' => $_POST['event_id']));
		}
		else
		{
			$stmt = $db->prepare("INSERT INTO eventuser (user_id, event_id) VALUES (:user_id, :event_id)");
			$stmt->execute(array(':user_id' => $user_id, ':event_id' => $_POST['event_id']));
		}
		header("Location: event.php?id=" . $_POST['event_id']);
	}
	else if (isset($_POST['tag_id']))
	{
		$stmt = $db->prepare("SELECT * FROM usertag WHERE user_id = :user_id AND tag_id = :tag_id");
		$stmt->execute(array(':user_id' => $user_id, ':tag_id' => $_POST['tag_id']));
		if ($stmt->rowCount() > 0)
		{
			$stmt = $db->prepare("DELETE FROM usertag WHERE user_id = :user_id AND tag_id = :tag_id");
			$stmt->execute(array(':user_id' => $user_id, ':tag_id' => $_POST['tag_id']));
		}
		else
		{
			$stmt = $db->prepare("INSERT INTO usertag (user_id, tag_id) VALUES (:user_id, :tag_id)");
			$stmt->execute(array(':user_id' => $user_id, ':tag_id' => $_POST['tag_id']));
		}
		header("Location: tag.php?id=" . $_POST['tag_id']);
	}
 ?>
