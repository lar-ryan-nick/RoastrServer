<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$user1 = $_GET['user1'];
$user2 = $_GET['user2'];
$index = $_GET['index'];

$sql = "SELECT * FROM messages WHERE (sender = $user1 AND receiver = $user2) OR (sender = $user2 AND receiver = $user1) ORDER BY timeSent";
$result = pg_query($conn, $sql);
if (pg_num_rows($result) > 0)
{
    for ($i = 0; $i < $index; $i++)
    {
		$row = pg_fetch_array($result);
    }
	$sql = "SELECT username, profilePicture FROM users WHERE id = " . $row['sender'];
	$result = pg_query($conn, $sql);
	if ($row2 = pg_fetch_array($result))
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
		echo "Error: " . $sql . "<br>" . pg_last_error($conn);
	}
}
else if (pg_num_rows($result) === 0)
{
    echo "No messages";
}
else
{
    echo "Error: " . $sql . "<br>" . pg_last_error($conn);
}
pg_close($conn);
?>
