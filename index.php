<?php
  require_once('includes/config.php');

  $search = $_GET["keyword"];
	$stmt = $db->prepare('SELECT event_id, event_name, event_time, duration FROM event WHERE is_inactive = 0 ORDER BY event_time asc');
	$stmt->execute();

  $title = 'UNC Events Online';

  //include header template
  echo '<body>';
  require('layout/header.php');
  ?>
  <!-- Page Content -->
  <div class="container">
    <div class="row">
      <div class="col-lg-12 text-center">
        <h1 class="mt-5">Events</h1>
        <!-- <p class="lead">Complete with pre-defined file paths and responsive navigation!</p> -->
        <!-- <ul class="list-unstyled"> -->
          <!-- <li>Bootstrap 4.3.1</li>
          <li>jQuery 3.4.1</li> -->
        <!-- </ul> -->
      </div>
    </div>
</div>

  <?php
    while($row = $stmt -> fetch(PDO::FETCH_ASSOC))
    {
			$item = array(
        'event_id'   => $row['event_id'],
        'event_name' => $row['event_name'],
        'event_time' => $row['event_time'],
        'duration' => $row['duration'],
      );
      $phptime = strtotime($item['event_time']);
      $time = date("m/d/y g:i A", $phptime);
			echo '<div class="row"><div class="col-lg-12 text-center">
          <h3><a href="event.php?id='.$item['event_id'].'">'.$item['event_name'].'</a></h3>
          <p>'.$time.'</p>
        <p>'.$item['duration'].'</p>
      </div></div><br><br>';
    }
  ?>
<!-- require('layout/footer.php') -->
