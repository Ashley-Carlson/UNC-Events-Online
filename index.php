<?php
  require_once('includes/config.php');

  $search = $_GET["keyword"];
	$event_stmt = $db->prepare('SELECT event_id, event_name, event_time, duration, photo_path FROM event WHERE is_inactive = 0 AND event_time >= NOW() ORDER BY event_time asc');
	$event_stmt->execute();

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
<!-- Idk how to get this to work, but something with this to filter and hide the posts on the index -Casey Burklow
<select name="index-filter[]" data-placeholder="Filter tags..." multiple class="chosen-select" onchange="filterSelection(this)">
	<option value=""></option>
	<option value="1">Biology</option>
	<option value="2">Mathematics</option>
	<option value="3">Technology</option>
	<option value="4">Art</option>
	<option value="5">Science</option>
	<option value="6">Performance</option>
	<option value="7">Theater</option>
	<option value="8">Chemistry</option>
	<option value="9">Culture</option>
	<option value="10">Cuisine</option>
	<option value="11">Animals</option>
	<option value="12">21+</option>
	<option value="13">Official</option>
	<option value="14">Party</option>
	<option value="15">Greek</option>
	<option value="16">Mechanics</option>
	<option value="17">Engineering</option>
	<option value="18">AI</option>
	<option value="19">Business</option>
	<option value="20">Networking</option>
	<option value="21">Food</option>
	<option value="22">Sports</option>
	<option value="23">Football</option>
	<option value="24">Volleyball</option>
	<option value="25">Soccer</option>
</select>
-->

  <?php
    $row_start = 1;
    while($row = $event_stmt->fetch(PDO::FETCH_ASSOC))
    {
			$item = array(
        'event_id'   => $row['event_id'],
        'event_name' => $row['event_name'],
        'event_time' => $row['event_time'],
        'duration' => $row['duration'],
        'photo_path' => $row['photo_path'],
        'row_start' => ($row_start == 1)
      );
      if ($row_start == 1) {
        $row_start = 0;
      }
      else {
        $row_start = 1;
      }
			$stmt = $db->prepare("
			SELECT eventtag.tag_id as tag_id, tag.tag AS tag
			  FROM eventtag
 	 LEFT JOIN tag ON tag.tag_id = eventtag.tag_id
			 WHERE eventtag.event_id = :event_id
	 ");
			$stmt->execute(array(":event_id" => $item["event_id"]));
      $phptime = strtotime($item['event_time']);
      $time = date("m/d/y g:i A", $phptime);
			$tagstring = "";
			while ($tag = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$tagstring .= '<a href="search.php?type=event&keyword=' . $tag['tag']. '">' . $tag['tag'] . "</a> ";
			}


      $filepath = isset($item['photo_path']) ? $item['photo_path'] : "media/logo.png";
    /*  if ($item['row_start'] == True) {
        echo '<div class="row">';
      } <div class = "column"> */
      echo '<div class = "card">
            <div class="card-image"><img src='. $filepath .' alt="UNC" id="card-image"></div>
            <div class="container">
              <h3><a href="event.php?id='.$item['event_id'].'">'.$item['event_name'].'</a></h3>
              <p><u>Date and Time:</u>     '.$time.'</p>
              <p><u>Duration of Event:</u> '.$item['duration'].'</p>
    			    <p><u>Tags:</u>              '.$tagstring.'</p>
            </div>
          </div>
          &nbsp';
    /*  if ($item['row_start'] == True) {
        echo '</div>';
      }
      echo '&nbsp'; if you bring this line back, remove &nbsp from line 106 */

    }
// require('layout/footer.php')
?>
