<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$user = $_GET['user'];
$post = $_GET['post'];
$user2 = $_GET['user2'];

$sql = "SELECT post, COUNT(post) AS magnitude FROM likes GROUP BY post ORDER BY COUNT(post) DESC";

if ($result = pg_query($conn, $sql)) 
{
	for ($i = 0; $i < $post && $row = pg_fetch_array($result); $i++)
    {
		$sql = "SELECT * FROM posts WHERE id = " . $row['post'];
    	$result2 = pg_query($conn, $sql);
    	if ($row2 = pg_fetch_array($result2))
    	{
			if ($user > 0 && $row2['user'] != $user)
			{
				$i--;
			}
		}
    	else
    	{
        	echo "Shit Error: " . $sql . "<br>" . pg_last_error($conn);
    	}
    }
		$filename = $row2['image'];
		$file = fopen("images/$filename", "r");
		$data = fread($file, filesize("images/$filename"));
		fclose($file);
		$image = base64_encode($data);
		$sql = "SELECT username, profilePicture FROM users WHERE id = " . $row2['user'];
		if ($result = pg_query($conn, $sql))
		{
			$row3 = pg_fetch_array($result);
			$filename2 = $row3['profilePicture'];
			$file = fopen("profilePictures/$filename2", "r");
			$data = fread($file, filesize("profilePictures/$filename2"));
			fclose($file);
			$profilePicture = base64_encode($data);
		}
		else
		{
			echo "Fuck Error: $sql <br>" . pg_last_error($conn);
		}
		$sql = "SELECT id FROM comments WHERE post = ". $row2['id'];
		$result = pg_query($conn, $sql);
		if (!$result)
		{
			echo "Ass Error: $sql <br>" . pg_last_error($conn);
		}
		$sql = "SELECT \"user\" FROM likes WHERE post = " . $row2['id'];
		if ($result2 = pg_query($conn, $sql))
		{
			$liked = false;
			while ($row4 = pg_fetch_array($result2))
			{
				if ($row4['user'] == $user2)
				{
					$liked = true;
					break;
				}
			}
		}
		$postData = array('imageFilename' => $filename, 'profileFilename' => $filename2, 'id' => $row2['id'], 'user' => $row2['user'], 'timePosted' => $row2['timePosted'], 'caption' => $row2['caption'], 'image' => $image, 'username' => $row3['username'], 'likes' => pg_num_rows($result2) - 1, 'liked' => $liked, 'comments' => pg_num_rows($result), 'profilePicture' => $profilePicture);
	header( 'Content-type: application/json; charset=utf-8' );
		echo json_encode($postData);
}	 
else 
{
    echo "Shit Error: " . $sql . "<br>" . pg_last_error($conn);
}

pg_close($conn);
?>
