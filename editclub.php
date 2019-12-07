<?php require("includes/config.php");

$id = $_POST['id2'];
echo '<body style="background-color: white;">';
if (!$user->is_logged_in()) {
	header("Location: index.php");
}

$stmt = $db -> prepare('SELECT user_id, acct_type FROM user WHERE username = :username');
$stmt -> execute(array(
  ':username' => $_SESSION['username'],
));

$row = $stmt -> fetch(PDO::FETCH_ASSOC);
$userID = $row['user_id'];


$edit_stmt = $db->prepare('SELECT can_edit FROM clubmember WHERE user_id = :user_id AND club_id = :club_id');
$edit_stmt->execute(array(':user_id' => $userID, ':club_id' => $_POST['id2']));
$can_edit = $edit_stmt->fetch(PDO::FETCH_ASSOC);
if ($can_edit['can_edit'] != 1 && $row['acct_type'] != 2)
{
  header("Location: index.php");
}

if (isset($_POST['id']))
{
  $stmt = $db ->prepare(
'UPDATE club
 SET
   club_name = :name,
	 club_desc = :description,
	 fac_sponsor_id = :sponsor_id
 WHERE club_id = :id');
  $stmt -> execute(array(
  ':name' => $_POST['name'],
	':description' => $_POST['description'],
	':sponsor_id' => $sponsor_id,
	':id' => $_POST['id2']
  ));
	if (!empty($_FILES['image'])
	 && file_exists($_FILES['image']['tmp_name'])
	 && is_uploaded_file($_FILES['image']['tmp_name'])
	)
	{
		if ($_FILES['image']['size'] > 1000000)
		{
			throw new RuntimeException('Exceeded filesize limit.');
		}
    $finfo = new finfo(FILEINFO_MIME_TYPE);
		if (false === $ext = array_search(
			 $finfo->file($_FILES['image']['tmp_name']),
			 array(
					 'jpg' => 'image/jpeg',
					 'png' => 'image/png',
					 'gif' => 'image/gif',
			 ),
			 true
	 )) {
			 throw new RuntimeException('Invalid file format.');
	 }
		mkdir("media/clubs/" . $_POST['id2'], 0777, true);
		$directory = "media/clubs/" . $_POST['id2'] . "/";
		$target = $directory . sha1_file($_FILES['image']['tmp_name']) . '.' . $ext;
		$filename = sha1_file($_FILES['image']['tmp_name']) . '.' . $ext;
		move_uploaded_file($_FILES['image']['tmp_name'], $target);

		$stmt = $db->prepare("UPDATE club SET photo_path = :photo WHERE club_id = :club_id");
		$stmt->execute(array(':photo' => $target, ':club_id' => $_POST['id2']));

		echo '<p class="success">File uploaded.</p>';
	}

  header("Location: club.php?id=".$_POST['id']);
}


$stmt = $db -> prepare(
	'SELECT
	  club_name,
		photo_path,
		club_desc,
		fac_sponsor_id
	 FROM club
	 WHERE club_id = :id'
);
$stmt -> execute(array(
  ':id' => $id,
));

$club = $stmt -> fetch(PDO::FETCH_ASSOC);

require('layout/header.php');

?>

<script type="text/javascript">
// Chosen filtering
$(function() {
	$(".chosen-select").chosen();
});
</script>

	<form enctype="multipart/form-data" role="form" action="editclub.php" method="POST">
		<div class="container">
			<body class="addclub">
				<h1 class="title">Edit Club</h1>
				<hr>
				<!-- takes text input for title, description, reserve -->
				<b>Club Name: </b>
				<input type="text" name="name" value="<?php echo $club['club_name'] ?>"><br>

				<b>Description:</b><br>
				<textarea id="subject" name="description"style="width:30%;height:20%;color:#000000"><?php echo $club['club_desc'] ?></textarea><br>

				<b>Faculty Sponsor:</b><br>
					<select name="sponsor_id">
						<?php
						$stmt = $db->prepare("SELECT CONCAT(first_name, ' ', last_name) as name, user_id FROM user WHERE acct_type = 2 ORDER BY user_id");
						$stmt->execute();
						while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
						{
							$selected = $row['user_id'] == $club['fac_sponsor_id'] ? " selected" : "";
							echo '<option value="' . $row['user_id'] . '"'.$selected.'>' . $row['name'] . '</option>';
						}
						?>
					</select><br>

				<b>Tags:</b><br>
				<select data-placeholder="Begin typing to filter tags..." multiple class="chosen-select" name="tags[]">
					<option value=""></option>
					<?php
					$tagfetch = $db->prepare("SELECT tag_id FROM clubtag WHERE club_id = :club_id");
					$tagfetch->execute(array(':club_id' => $id));
					$matchtags = $tagfetch->fetchAll(PDO::FETCH_COLUMN, 0);
					$stmt = $db->prepare("SELECT tag_id, tag FROM tag ORDER BY tag_id");
					$stmt->execute();
					while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
					{
						$selected = in_array($row['tag_id'], $matchtags) ? " selected" : "";
						echo '<option value="' . $row['tag_id'] . '"' . $selected . '>' . $row['tag'] . '</option>';
					}
					?>
				</select><br>

				<b>Photo:</b><br>
				<input type="file" name="image" id="image"><br>

				<!-- actual file upload for the item itself -->
				<!-- Upload Image:
				<input type="file" name="image" id="image"> -->

				<input type="checkbox" required><b> I agree that my event abides by the following the
					<a href="https://www.unco.edu/clubs-organizations/pdf/RSO-Manual.pdf">policy manual</a>,
					<a href="https://www.unco.edu/clubs-organizations/pdf/2018-2019-rso-constitution-guide.pdf">constitutional guidelines </a>
					and will submit an <a href="https://www.unco.edu/clubs-organizations/pdf/archiving-rso-records.pdf">archives request</a> (if necessary) for this event.
		</b><br>
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
