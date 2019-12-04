<?php require("includes/config.php");

$stmt = $db->prepare('SELECT user_id, acct_type FROM user where username = :username');
$stmt->execute(array(':username' => $_SESSION['username']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$userInfo = array('acct_type' => $row['acct_type'], 'user_id' => $row['user_id']);

$id = $_POST['id3'];

$stmt = $db->prepare(
	'SELECT can_edit
	   FROM clubmember
		WHERE club_id = :club_id
		  AND user_id = :user_id'
);
$stmt->execute(array(':club_id'=>$id, ':user_id' => $userInfo['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$canEdit = $row['can_edit'];

if ($userInfo['acct_type'] != 2 && $canEdit != 1) {
    header("Location: index.php");
}

$stmt = $db->prepare('DELETE FROM clubfollower WHERE club_id = :id');
$stmt->execute(array(':id' => $id));
$stmt = $db->prepare('DELETE FROM clubmember WHERE club_id = :id');
$stmt->execute(array(':id' => $id));
$stmt = $db->prepare('DELETE FROM clubtag WHERE club_id = :id');
$stmt->execute(array(':id' => $id));
$stmt = $db->prepare('DELETE FROM club where club_id = :id');
$stmt->execute(array(':id' => $id));

header('Location: index.php');

?>
