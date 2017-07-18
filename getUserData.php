<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$user = $_GET['arg1'];
$user2 = $_GET['arg2'];

$sql = "SELECT username, profilePicture FROM users WHERE id = $user";

if ($result = pg_query($conn, $sql)) 
{
	$row = pg_fetch_array($result);
	$filename = $row['profilePicture'];
	if (!$filename)
	{
		$filename = "roastrtransparent.png";
	}
	$file = fopen("profilePictures/$filename", "r");
	$data = fread($file, filesize("profilePictures/$filename"));
	fclose($file);
	$picture = base64_encode($data);
	$sql = "SELECT id FROM posts WHERE \"user\" = $user";
	if ($result = pg_query($conn, $sql))
	{
		$numPosts = pg_num_rows($result);
	}
	else
	{
		echo "Fuck Error: $sql <br>" . pg_last_error($conn);
	}
	$numLikes = 0;
	while($row2 = pg_fetch_array($result))
	{
		$sql = "SELECT id FROM likes WHERE post = ". $row2['id'];
		if ($result2 = pg_query($conn, $sql))
		{
			$numLikes += pg_num_rows($result2) - 1;
		}
		else
		{
			echo "Ass Error: $sql <br>" . pg_last_error($conn);
		}
	}
	$sql = "SELECT requester, receiver, accepted FROM friends WHERE receiver = $user UNION 
			SELECT requester, receiver, accepted FROM friends WHERE requester = $user";
	$result = pg_query($conn, $sql);
	$numFriends = 0;
	$friends = -1;
	while ($row2 = pg_fetch_array($result))
	{
		if ($row2['accepted'] == 1)
		{
			$numFriends++;
		}
		if ($row2['requester'] == $user2)
		{
			if ($row2['accepted'] == 1)
			{
				$friends = 2;
			}
			else
			{
				$friends = 0;
			}
		}
		else if ($row2['receiver'] == $user2)
		{
			if ($row2['accepted'] == 1)
			{
				$friends = 2;
			}
			else
			{
				$friends = 1;
			}
		}
	}
	$userData = array('profileFilename' => $filename, 'profilePicture' => $picture, 'username' => $row['username'], 'likes' => $numLikes, 'posts' => $numPosts, 'friends' => $numFriends, 'friended' => $friends);
	echo json_encode($userData);
} 
else 
{
    echo "Shit Error: " . $sql . "<br>" . pg_last_error($conn);
}
pg_close($conn);
?>
