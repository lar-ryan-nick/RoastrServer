<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = $_GET['arg1'];
$user2 = $_GET['arg2'];

$sql = "SELECT username, profilePicture FROM users WHERE id = $user";

if ($result = mysqli_query($conn, $sql)) 
{
	$row = mysqli_fetch_assoc($result);
	$filename = $row['profilePicture'];
	if (!$filename)
	{
		$filename = "roastrtransparent.png";
	}
	$file = fopen("profilePictures/$filename", "r");
	$data = fread($file, filesize("profilePictures/$filename"));
	fclose($file);
	$picture = base64_encode($data);
	$sql = "SELECT id FROM posts WHERE user = $user";
	if ($result = mysqli_query($conn, $sql))
	{
		$numPosts = mysqli_num_rows($result);
	}
	else
	{
		echo "Fuck Error: $sql <br>" . mysqli_error($conn);
	}
	$numLikes = 0;
	while($row2 = mysqli_fetch_assoc($result))
	{
		$sql = "SELECT id FROM likes WHERE post = ". $row2['id'];
		if ($result2 = mysqli_query($conn, $sql))
		{
			$numLikes += mysqli_num_rows($result2) - 1;
		}
		else
		{
			echo "Ass Error: $sql <br>" . mysqli_error($conn);
		}
	}
	$sql = "SELECT requester, receiver, accepted FROM friends WHERE receiver = $user UNION 
			SELECT requester, receiver, accepted FROM friends WHERE requester = $user";
	$result = mysqli_query($conn, $sql);
	$numFriends = 0;
	$friends = -1;
	while ($row2 = mysqli_fetch_assoc($result))
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
    echo "Shit Error: " . $sql . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);
?>
