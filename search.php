<?php require_once('includes/config.php');
  $search = $_GET["keyword"];
  $stmt = $db->prepare('
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
    group_concat(tagget.tag) as tags
  FROM event
  INNER JOIN
    (
      SELECT
        eventtag.event_id,
        tag.tag
      FROM tag
      LEFT JOIN eventtag ON eventtag.tag_id = tag.tag_id
    ) as tagget ON event.event_id = tagget.id
  LEFT JOIN user
  ON
    user.user_id = event.event_contact
  WHERE
    CONCAT(event.event_name, tags, event.event_desc, user.username, user.email, user.first_name, user.last_name) LIKE :search;
  ');
  $stmt->bindValue('search', '%' . $search . '%');
  $stmt->execute();
?>

<!-- Page Content -->
<div class="container">
  <div class="row">
    <div class="col-lg-12 text-center">
      <h1 class="mt-5">Search Results</h1>
      <!-- <p class="lead">Complete with pre-defined file paths and responsive navigation!</p> -->
      <!-- <ul class="list-unstyled"> -->
        <!-- <li>Bootstrap 4.3.1</li>
        <li>jQuery 3.4.1</li> -->
      <!-- </ul> -->
    </div>
  </div>
</div>

<?php
  while($row = $event_stmt->fetch(PDO::FETCH_ASSOC))
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
?>
