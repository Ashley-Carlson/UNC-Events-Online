<?php
  require_once('includes/config.php');

  $search = $_GET["keyword"];
	$stmt = $db->prepare('SELECT event_id, event_name, event_time, event_desc FROM event WHERE is_inactive = 0 ORDER BY event_time asc');
	$stmt->execute();

  $title = 'UNC Events Online';

  //include header template
  require('layout/header.php');
  echo '
   <div class="card">
     <h1 style="color:White; text-align:center;">Events</h1>
   </div>
   <br><br>
   ';

		while($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
			$item = array(
        'event_id'   => $row['event_id'],
        'event_name' => $row['event_name'],
        'event_desc' => $row['event_desc'],
        'event_time' => $row['event_time'],
      );
      $phptime = strtotime($item['event_time']);
      $time = date("m/d/y g:i A", $phptime);
			echo '<div class="card">
          <h3 style="color: #eacc1f; text-align:center;"><a href="event.php?id='.$item['event_id'].'">'.$item['event_name'].'</a></h3>
          <p>'.$time.'</p>
        <p style="word-wrap:break-word;">'.$item['event_desc'].'</p>
      </div><br><br>';
		}
require('layout/footer.php') 
?>
