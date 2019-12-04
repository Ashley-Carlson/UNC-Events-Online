<?php require_once('includes/config.php');
  $search = $_GET["keyword"];
	$type = $_GET["type"];
	if (!isset($type))
	{
		$type = "event";
	}
	if ($type == "event")
	{
	  $search_stmt = $db->prepare('
	  SELECT
	    event.event_id,
	    event.event_name,
	    event.event_time,
	    event.duration,
	    event.event_desc,
	    user.username,
	    user.email,
	    user.first_name,
	    user.last_name,
	    coalesce(group_concat(tag.tag), "") as tags
	  FROM
	    event
	  LEFT JOIN user ON user.user_id = event.event_contact
	  LEFT JOIN eventtag on eventtag.event_id = event.event_id
	  LEFT JOIN tag ON eventtag.tag_id = tag.tag_id
	  GROUP BY event_id
	  HAVING
	    CONCAT(event_name, tags, event_desc, username, email, first_name, last_name) LIKE :search
	  ');
	  $search_stmt->bindValue('search', '%' . $search . '%');
	  $search_stmt->execute();
	}
	else if ($type == "club")
	{
		$search_stmt = $db->prepare('
		SELECT
		  club.club_id,
			club.club_name,
			club.club_desc,
			user.username,
			user.first_name,
			user.last_name,
			user.email,
			coalesce(group_concat(tag.tag), "") as tags
		FROM
		  club
		LEFT JOIN user ON user.user_id = club.fac_sponsor_id
		LEFT JOIN clubtag ON clubtag.club_id = club.club_id
		LEFT JOIN tag ON clubtag.tag_id = tag.tag_id
		GROUP BY club.club_id
		HAVING
		  CONCAT(club_id, club_name, club_desc, username, first_name, last_name, email, tags) LIKE :search
		');
		$search_stmt->bindValue('search', '%' . $search . '%');
		$search_stmt->execute();
	}
  $title = "Search: ".$search;
  echo '<body>';
  require('layout/header.php');
?>

<!-- Page Content -->
<div class="container">
  <div class="row">
    <div class="col-lg-12 text-center">
      <h1 class="mt-5">Search Results</h1>
			<?php echo '<h3>Searched '. $type . 's for ' . $search . '</h3>' ?>
      <!-- <p class="lead">Complete with pre-defined file paths and responsive navigation!</p> -->
      <!-- <ul class="list-unstyled"> -->
        <!-- <li>Bootstrap 4.3.1</li>
        <li>jQuery 3.4.1</li> -->
      <!-- </ul> -->
    </div>
  </div>
</div>

<?php
  if ($type == "event") {
	  while($row = $search_stmt->fetch(PDO::FETCH_ASSOC))
	  {
	    $item = array(
	      'event_id'   => $row['event_id'],
	      'event_name' => $row['event_name'],
	      'event_time' => $row['event_time'],
	      'duration' => $row['duration'],
	    );
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
	      $tagstring .= $tag['tag'] . " ";
	    }
	    echo '<div class="row"><div class="col-lg-12 text-center">
	        <h3><a href="event.php?id='.$item['event_id'].'">'.$item['event_name'].'</a></h3>
	        <p>'.$time.'</p>
	      <p>'.$item['duration'].'</p>
	      <p>'.$tagstring.'</p>
	    </div></div><br><br>';
	  }
	}
	else if ($type == "club")
	{
		while($row = $search_stmt->fetch(PDO::FETCH_ASSOC))
	  {
	    $item = array(
	      'club_id'   => $row['club_id'],
	      'club_name' => $row['club_name'],
	      'club_desc' => $row['club_desc'],
	      'fac_sponsor' => $row['first_name'] . ' ' . $row['last_name'],
	    );
	    $stmt = $db->prepare("
	    SELECT clubtag.tag_id as tag_id, tag.tag AS tag
	      FROM clubtag
	 LEFT JOIN tag ON tag.tag_id = clubtag.tag_id
	     WHERE clubtag.club_id = :club_id
	 ");
	    $stmt->execute(array(":club_id" => $item["club_id"]));
	    $tagstring = "";
	    while ($tag = $stmt->fetch(PDO::FETCH_ASSOC))
	    {
	      $tagstring .= $tag['tag'] . " ";
	    }
	    echo '<div class="row"><div class="col-lg-12 text-center">
	        <h3><a href="club.php?id='.$item['club_id'].'">'.$item['club_name'].'</a></h3>
	        <p>'.$item['club_desc'].'</p>
	      <p>'.$item['fac_sponsor'].'</p>
	      <p>'.$tagstring.'</p>
	    </div></div><br><br>';
	  }
	}
	else
	{
		echo "<h3>Cannot search " . $type . " at this point in time. Please search in clubs or events.</h3>";
	}
?>
