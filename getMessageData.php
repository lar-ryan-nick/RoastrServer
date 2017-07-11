<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user1 = $_GET['user1'];
$user2 = $_GET['user2'];
$index = $_GET['index'];

$sql = "SELECT * FROM messages WHERE (sender = $user1 AND receiver = $user2) OR (sender = $user2 AND receiver = $user1) ORDER BY timeSent";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0)
{
    for ($i = 0; $i < $index; $i++)
    {
		$row = mysqli_fetch_assoc($result);
    }
	$sql = "SELECT username, profilePicture FROM users WHERE id = " . $row['sender'];
	$result = mysqli_query($conn, $sql);
	if ($row2 = mysqli_fetch_assoc($result))
	{
		$filename = $row2['profilePicture'];
		$file = fopen("profilePictures/$filename", "r");
		$data = fread($file, filesize("profilePictures/$filename"));
		$picture = base64_encode($data);
		$likeUsers = array('username' => $row2['username'], 'message' => $row['message'], 'id' => $row['id'], 'user' => $row['sender'], 'timeSent' => $row['timeSent'], 'profilePicture' => $picture);
		echo json_encode($likeUsers);
	}
	else
	{
		echo "Error: " . $sql . "<br>" . mysqli_error($conn);
	}
}
else if (mysqli_num_rows($result) === 0)
{
    echo "No messages";
}
else
{
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);
?>
