<?php
require_once("includes/config.php");

if (!$user->is_logged_in()) {
	header("Location: index.php");
}

$stmt = $db->prepare('SELECT user_id, username, email, first_name, acct_type FROM user where username = :username');
$stmt->execute(array(':username' => $_SESSION['username']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$userInfo = array('user_id'=>$row['user_id'], 'username'=>$row['username'], 'email'=>$row['email'], 'firstName'=>$row['firstName'], 'acct_type'=>$row['acct_type']);

$stmt = $db->prepare('SELECT event_id, event_name FROM event where event_contact = :contact_id and is_inactive = 0');
$stmt->execute(array(':contact_id' => $userInfo['user_id']));

if ($userInfo['acct_type'] == 2) {
	$admin = true;
}

$title = $currentUser;
require("layout/header.php");
?>
<div class="card">
    <h1>Your Profile</h1>
    <br />
    <h2>Your Events</h2>
    <br />
    <ul>
    <?php

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $item = array('id'=>$row['event_id'], 'title'=>$row['event_name']);
            echo '<li><a href="event.php?id=' . $item['id'] . '">' . $item['title'] . '</a></li><br />';
        }

    ?>
		</div>
    </ul>

    <br />
		<div class="card">
    <h2>Update Your Profile:</h2>
    <ul>
      <li><a href="addevent.php">Add New Event</a></li>
      <li><a href="updateuser.php">Change Information</a></li>
			<?php if ($admin) { echo '<li><a href="tools.php">Admin Tools</a></li>'; } ?>
    </ul>
		</div>

<?php

require("layout/footer.php");

?>
