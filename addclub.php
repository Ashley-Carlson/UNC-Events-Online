<?php
require_once("includes/config.php");
// assigns the "add new item" string to the $title variable
$title = 'Create New Club';
// put the header into the page
require("layout/header.php");
// if the user is logged in, put their name in the header
if (!$user->is_logged_in()) {
	header("Location: index.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST))
{

  $stmt = $db->prepare('SELECT user_id FROM user where username = :username');
  $stmt->execute(array(':username' => $_SESSION['username']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $userID = $row['user_id'];

  $event_date = date("Y-m-d H:i:s",strtotime($_POST['event_time']));
  $stmt = $db->prepare('INSERT INTO club (club_name, club_desc, fac_sponsor_id,
  photo_path) VALUES (:name, :description,
  :sponsor_id, :photo_url)');
  $stmt -> execute(array(
  ':name' => $_POST['name'],
	':description' => $_POST['description'],
	':sponsor_id' => $sponsor_id,
	':photo_url' => $_POST['photo_url'],
  ));


  echo '<p class="success">Club created.</p>';

	$stmt = $db->prepare('SELECT MAX(club_id) as m FROM club');
	$stmt -> execute();
	$row = $stmt -> fetch(PDO::FETCH_ASSOC);

	if ($_POST['tags'])
	{
		$stmt = $db->prepare('INSERT INTO clubtag (club_id, tag_id) VALUES (:club_id, :tag_id)');
		foreach($_POST['tags'] as $tag_id)
		{
			$stmt->execute(array(':club_id' => $row['m'], ':tag_id' => $tag_id));
		}
	}

	$stmt = $db->prepare('INSERT INTO clubmember (user_id, club_id, is_contact, can_edit) VALUES (:user_id, :club_id, 1, 1)');
	$stmt->execute(array(":user_id" => $userID, ":club_id" => row['m']));

	header("Location: club.php?id=".$row['m']);
}
?>

<script type="text/javascript">
// Chosen filtering
$(function() {
	$(".chosen-select").chosen();
});
</script>

<form role="form" action="addclub.php" method="POST">
	<div class="container">
		<body class="addclub">
			<h1 class="title">Add a Club</h1>
			<hr>
			<!-- takes text input for title, description, reserve -->
			<b>Club Name: </b>
			<input type="text" name="name" placeholder="Event Name"></b><br>

			<b>Description:</b><br>
			<textarea id="subject" name="description" placeholder="Write something..." style="width:30%;height:20%;color:#000000"></textarea><br>

			<b>Faculty Sponsor:</b><br>
				<select name="sponsor_id">
					<?php
					$stmt = $db->prepare("SELECT CONCAT(first_name, ' ', last_name) as name, user_id FROM user WHERE acct_type = 2 ORDER BY user_id");
					$stmt->execute();
					while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
					{
						echo '<option value="' . $row['user_id'] . '">' . $row['name'] . '</option>';
					}
					?>
				</select><br>

			<b>Tags:</b><br>
			<select data-placeholder="Begin typing to filter tags..." multiple class="chosen-select" name="tags[]">
				<option value=""></option>
				<?php
				$stmt = $db->prepare("SELECT tag_id, tag FROM tag ORDER BY tag_id");
				$stmt->execute();
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
				{
					echo '<option value="' . $row['tag_id'] . '">' . $row['tag'] . '</option>';
				}
				?>
			</select><br>

			<b>Photo:</b><br>
			<input type="file" name="image" id="image"><br>

			<!-- actual file upload for the item itself -->
			<!-- Upload Image:
			<input type="file" name="image" id="image"> -->

			<input type="checkbox" required><b> Do you agree to follow all club and event policies as defined by the UNC Office of Student Organizations?</b><br>

			<!-- submits the data entered to the server -->
			<input type="submit" value="Submit" id="popUpYes" color: white >
		</body>
	</div>
</form>

<!-- For tag filtering -->
<script src="layout/jquery/jquery.min.js"></script>
<script src="layout/chosen/chosen.jquery.min.js"></script>
<link href="layout/chosen/chosen.min.css" rel="stylesheet"/>

<?php require('layout/footer.php') ?>
