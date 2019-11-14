<?php require_once('includes/config.php');

  $currentID = $_GET["id"];
	$stmt = $db->prepare("
   SELECT event.event_name as event_name, event.event_desc as event_desc,
          event.has_food as has_food, event.event_time as event_time,
          event.location as location, user.first_name as first_name,
          user.last_name as last_name, user.email as email
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
  );

  $stmt = $db -> prepare("
  SELECT user_id
    FROM user
   WHERE username = :username
  ");
  $stmt -> execute(array(':username' => $_SESSION['username']));
  $row = $stmt -> fetch(PDO::FETCH_ASSOC);
  

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
     <h3 style="color: #ffffff; text-align:center;"><?php echo $item['description'] ?></h3>
     <h3 style="color: #ffffff; text-align:center;"><?php echo $time ?></h3>
     </div>
     <div class="card" style="color: White;">
       <!--<h2>Faculty Sponsor: </h2>-->
       <h2>Contact Name: <?php echo $item['first_name'].' '.$item['last_name'] ?></h2>
       <p>Contact email: <?php echo $item['email'] ?></p>
     </div>
     <div class="card" style="color: White;">
       <p><?php echo $item['location'] ?></p>
       </div>

<?php require('layout/footer.php') ?>
