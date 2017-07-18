<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$user = $_GET['arg1'];
$post = $_GET['arg2'];
$user2 = $_GET['arg3'];

if ($user == -1)
{
	$sql = "SELECT * FROM POSTS WHERE ID = $post";
}
else if ($user == 0)
{
	$sql = "SELECT * FROM POSTS ORDER BY TIMEPOSTED";
}
else
{
	$sql = "SELECT * FROM POSTS WHERE \"user\" = $user ORDER BY TIMEPOSTED";
}

if ($result = pg_query($conn, $sql)) 
{
	if ($user == -1)
	{
		$row = pg_fetch_array($result);
	}
	else
	{
    	for ($i = 0; $i < $post && $row = pg_fetch_array($result); $i++)
    	{
    	}
	}
	$filename = $row['image'];
	$file = fopen("images/$filename", "r");
	$data = fread($file, filesize("images/$filename"));
	fclose($file);
	$image = base64_encode($data);
	$sql = "SELECT USERNAME, PROFILEPICTURE FROM USERS WHERE ID = " . $row['user'];
	if ($result = pg_query($conn, $sql))
	{
		$row2 = pg_fetch_array($result);
		$filename2 = $row2['profilePicture'];
		if ($filename2 == "")
		{
			$filename2 = "roastrtransparent.png";
		}
		$file = fopen("profilePictures/$filename2", "r");
		$data = fread($file, filesize("profilePictures/$filename2"));
		fclose($file);
		$profilePicture = base64_encode($data);
	}
	else
	{
		echo "Fuck Error: $sql <br>" . pg_last_error($conn);
	}
	$sql = "SELECT ID FROM COMMENTS WHERE POST = ". $row['id'];
	$result = pg_query($conn, $sql);
	if (!$result)
	{
		echo "Ass Error: $sql <br>" . pg_last_error($conn);
	}
	$liked = false;
	$result2;
	$sql = "SELECT USER FROM LIKES WHERE POST = " . $row['id'];
	if ($result2 = pg_query($conn, $sql))
	{
		$liked = false;
		while ($row3 = pg_fetch_array($result2))
		{
			if ($row3['user'] == $user2)
			{
				$liked = true;
				break;
			}
		}
	}
	$postData = array('imageFilename' => $filename, 'profileFilename' => $filename2, 'image' => $image, 'id' => $row['id'], 'user' => $row['user'], 'timePosted' => $row['timePosted'], 'caption' => $row['caption'], 'username' => $row2['username'], 'likes' => pg_num_rows($result2) - 1, 'liked' => $liked, 'comments' => pg_num_rows($result), 'profilePicture' => $profilePicture);	
	header( 'Content-type: application/json; charset=utf-8' );
	$jsonStr = json_encode($postData);
	echo $jsonStr;
} 
else 
{
    echo "Shit Error: " . $sql . "<br>" . pg_last_error($conn);
}

pg_close($conn);
?>
