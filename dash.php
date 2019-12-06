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
$club_stmt = $db->prepare(
"SELECT
  club.club_id,
	club.club_name,
 FROM club
 LEFT JOIN clubmember ON club.club_id = clubmember.club_id
 WHERE clubmember.user_id = :user_id
   AND clubmember.can_edit = 1
"
);
$club_stmt->execute(array(":user_id" => $userInfo['user_id']));

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
    <?php

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $item = array('id'=>$row['event_id'], 'title'=>$row['event_name']);
            echo '<a href="event.php?id=' . $item['id'] . '">' . $item['title'] . '</a><br />';
        }

    ?>
    <br />
    <h2>Your Clubs</h2>
    <br />
    <?php
		while ($row = $club_stmt->fetch(PDO::FETCH_ASSOC)) {
				$item = array('id'=>$row['club_id'], 'title'=>$row['club_name']);
				echo '<a href="club.php?id=' . $item['id'] . '">' . $item['title'] . '</a><br />';
		}
    ?>

		<br>
		<br>
		<br>
    <h2>Add Clubs and Events:</h2>
    <ul>
      <li><a href="addclub.php">Add New Club</a></li>
      <li><a href="addevent.php">Add New Event</a></li>
			<?php if ($admin) { echo '<li><a href="tools.php">Admin Tools</a></li>'; } ?>
    </ul>
		</div>

<?php

require("layout/footer.php");

?>
