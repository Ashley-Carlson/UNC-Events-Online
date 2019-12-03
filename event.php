<?php require_once('includes/config.php');
  $currentID = $_GET["id"];
	$stmt = $db->prepare("
   SELECT event.event_name as event_name, event.event_desc as event_desc,
          event.has_food as has_food, event.event_time as event_time,
          event.location as location, user.user_id as user_id,
          user.first_name as first_name, user.last_name as last_name,
          user.email as email
     FROM event
LEFT JOIN user ON user.user_id = event.event_contact
    WHERE event_id = :id
  ");
	$stmt->execute(array(':id' => $currentID));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$item = array(
    'name'        => $row['event_name'],
    'description' => $row['event_desc'],
    'has_food'    => $row['has_food'],
    'event_time'  => $row['event_time'],
    'location'    => $row['location'],
    'first_name'  => $row['first_name'],
    'last_name'   => $row['last_name'],
    'email'       => $row['email'],
    'user_id'     => $row['user_id'],
  );
  $stmt = $db -> prepare("
  SELECT user_id
    FROM user
   WHERE username = :username
  ");
  $stmt -> execute(array(':username' => $_SESSION['username']));
  $row = $stmt -> fetch(PDO::FETCH_ASSOC);
  $userID = $row['user_id'];
  $phptime = strtotime($item['event_time']);
  $time = date("m/d/y g:i A", $phptime);
	$title = $item['name'];
	require('layout/header.php');
?>
  <br><br>
   <div class="container">
     <h1 style="color: #eacc1f; text-align:center;"><?php echo $item['name'] ?></h1>
    </div>
     <div class="card" style="color: White;">
     <body style="background-color: #333;">
     <h3 style="color: black; text-align:center;"><?php echo $item['description'] ?></h3>
     <h3 style="color: black; text-align:center;"><?php echo $time ?></h3>
     </div>
     <div class="card" style="color: White;">
       <!--<h2>Faculty Sponsor: </h2>-->
       <h2  style="color: #000080;">Contact Name: <?php echo $item['first_name'].' '.$item['last_name'] ?></h2>
       <p style="color: #000080;">Contact email: <?php echo $item['email'] ?></p>
     </div>
     <div class="card" style="color: White;">
       <p style="color: #000080;"><?php echo $item['location'] ?></p>
     </div>
     <div>
       <p style="color: white;">There is food <input type="checkbox" name="has_food" <?php if ($item['has_food'] == 1) { echo 'checked="checked"'; } ?>>
       </p>
     </div>
     <?php
     if ($userID == $item['user_id'])
     {
      echo '
     <form action="editevent.php" method="post">
     <input type="hidden" name="id2" value="' . $currentID . '">
     <input type="submit" value="Edit">
     </form>
     <form action="deleteevent.php" method="post">
     <input type="hidden" name="id3" value="' . $currentID . '">
     <input type="submit" value="Delete">
     </form>';
   } ?>

<?php require('layout/footer.php') ?>
