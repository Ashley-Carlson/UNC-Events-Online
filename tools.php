<?php

require("includes/config.php");

$title = 'Admin Tools';
require("layout/header.php");
echo '<body style="background-color: white;">';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
}

$stmt = $db->prepare('SELECT acct_type FROM user where username = :username');
$stmt->execute(array(':username' => $_SESSION['username']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$userInfo = array('acct_type' => $row['acct_type']);

if ($userInfo['acct_type'] != 2) {
    header("Location: index.php");
}

$stmt = $db->prepare(
  "SELECT event.event_id as event_id, event.event_name as event_name,
          event.event_desc as event_desc, user.username as username
     FROM event
LEFT JOIN user
       ON event.event_contact = user.user_id"
);
$stmt->execute();

echo '<body style="background-color: #333"><table name="items">';
echo '<h2>Event Management</h2><br>';
echo '<tr>
      <th>ID</th>
      <th>Event Name</th>
      <th>Description</th>
      <th>Contact</th>
      <th>Options</th>
      </tr>';

while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
    $item = array(
      'id'          => $row['event_id'],
      'title'       => $row['event_name'],
      'description' => $row['event_desc'],
      'contact'     => $row['username']
    );
    echo '<tr>
          <td>' . $item['id'] . '</td>
          <td>' . $item['title'] . '</td>
          <td>' . $item['description'] . '</td>
          <td>' . $item['contact'] . '</td>
          <td>
          <form action="editevent.php" method="post">
          <input type="hidden" name="id2" value="' . $item['id'] . '">
          <input type="submit" value="Edit">
          </form>
          <form action="deleteitem.php" method="post">
          <input type="hidden" name="id3" value="' . $item['id'] . '">
          <input type="submit" value="Delete">
          </form>
          </td>
          </tr>';
}

echo '</table>';

$stmt = $db->prepare('SELECT * FROM user');
$stmt->execute();
echo '<br><h2>User Management</h2><br>';
echo '<table name="users">';

echo '<tr>
      <th>User ID</th>
      <th>Username</th>
      <th>First Name</th>
      <th>Last Name</th>
      <th>Email</th>
      <th>Options</th>
      </tr>';

while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
    $user = array(
      'user_id'  => $row['user_id'],
      'username' =>$row['username'],
      'firstName'=>$row['first_name'],
      'lastName' =>$row['last_name'],
      'email'    => $row['email']
    );
    echo '<tr>
          <td>' . $user['user_id'] . '</td>
          <td>' . $user['username'] . '</td>
          <td>' . $user['firstName'] . '</td>
          <td>' . $user['lastName'] . '</td>
          <td>' . $user['email'] . '</td>
          <td>
          <form action="edititem.php" method="post">
          <input type="hidden" name="username" value="' . $user['username'] . '">
          <input type="submit" value="Edit">
          </form>
          <form action="deleteuser.php" method="post">
          <input type="hidden" name="username2" value="' . $user['username'] . '">
          <input type="submit" value="Delete">
          </form>
          </td>
          </tr>';
}

echo '</table>';

require("layout/footer.php");

?>
