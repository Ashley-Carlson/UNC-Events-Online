<?php require("includes/config.php");

$stmt = $db->prepare('SELECT user_id, acct_type FROM user where username = :username');
$stmt->execute(array(':username' => $_SESSION['username']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$userInfo = array('acct_type'=>$row['acct_type'], 'user_id' => $row['user_id']);

$id = $_POST['id3'];

$stmt = $db->prepare('SELECT event_contact, photo_path FROM event where event_id = :id');
$stmt->execute(array(':id'=>$id));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$itemInfo = array('event_contact'=>$row['event_contact'], 'photo_path' => $row['photo_path']);

if ($userInfo['acct_type'] != 2 && $userInfo['user_id'] != $itemInfo['event_contact']) {
    header("Location: index.php");
}

$stmt = $db -> prepare (
"SELECT user.email as email, event.event_name
 FROM user
LEFT JOIN eventfollower ON eventfollower.user_id = user.user_id
WHERE event.event_id = :id"
);
$stmt -> execute(array(":id" => $id)); // Assuming that it posts to self with ID as a parameter
while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) // Get associative array
{
  $email = $row['email'];
  $event = $row['event_name'];
  $message = 'The event you were following has been deleted! Be sure to update your calendar.';
  emailNotifaction($message, $event, $email, $noreply_email_addr);
}

$stmt = $db->prepare('DELETE FROM eventfollower where event_id = :id');
$stmt->execute(array(':id' => $id));
$stmt = $db->prepare('DELETE FROM eventtag where event_id = :id');
$stmt->execute(array(':id' => $id));
$stmt = $db->prepare('UPDATE event SET is_inactive = 1 WHERE event_id = :id');
$stmt->execute(array(':id' => $id));

if (isset($itemInfo['photo_path']))
{
  $files = glob("media/events/" . $id);
	foreach($files as $file)
	{
		if (is_file($file))
		{
			unlink($file);
		}
	}
  rmdir("media/events/" . $id);
}

header('Location: index.php');

?>
