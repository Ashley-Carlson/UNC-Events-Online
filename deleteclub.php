<?php require("includes/config.php");

// $stmt = $db->prepare('SELECT user_id, acct_type FROM user where username = :username');
// $stmt->execute(array(':username' => $_SESSION['username']));
// $row = $stmt->fetch(PDO::FETCH_ASSOC);
// $userInfo = array('acct_type'=>$row['acct_type'], 'user_id' => $row['user_id']);

// $id = $_POST['id3'];

// $stmt = $db->prepare('SELECT event_contact FROM event where event_id = :id');
// $stmt->execute(array(':id'=>$id));
// $row = $stmt->fetch(PDO::FETCH_ASSOC);
// $itemInfo = array('event_contact'=>$row['event_contact']);

// if ($userInfo['acct_type'] != 2 && $userInfo['user_id'] != $itemInfo['event_contact']) {
//     header("Location: index.php");
// }


// $stmt = $db->prepare('DELETE FROM event where event_id = :id');
// $stmt->execute(array(':id' => $id));

// header('Location: index.php');

?>
