<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = $_GET['arg1'];
$post = $_GET['arg2'];
$user2 = $_GET['arg3'];

if ($user == -1)
{
	$sql = "SELECT * FROM posts WHERE id = $post";
}
else if ($user == 0)
{
	$sql = "SELECT * FROM posts ORDER BY timePosted";
}
else
{
	$sql = "SELECT * FROM posts WHERE user = $user ORDER BY timePosted";
}

if ($result = mysqli_query($conn, $sql)) 
{
	if ($user == -1)
	{
		$row = mysqli_fetch_assoc($result);
	}
	else
	{
    	for ($i = 0; $i < $post && $row = mysqli_fetch_assoc($result); $i++)
    	{
    	}
	}
	$filename = $row['image'];
	$file = fopen("images/$filename", "r");
	$data = fread($file, filesize("images/$filename"));
	fclose($file);
	$image = base64_encode($data);
	$sql = "SELECT username, profilePicture FROM users WHERE id = " . $row['user'];
	if ($result = mysqli_query($conn, $sql))
	{
		$row2 = mysqli_fetch_assoc($result);
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
		echo "Fuck Error: $sql <br>" . mysqli_error($conn);
	}
	$sql = "SELECT id FROM comments WHERE post = ". $row['id'];
	$result = mysqli_query($conn, $sql);
	if (!$result)
	{
		echo "Ass Error: $sql <br>" . mysqli_error($conn);
	}
	$liked = false;
	$result2;
	$sql = "SELECT user FROM likes WHERE post = " . $row['id'];
	if ($result2 = mysqli_query($conn, $sql))
	{
		$liked = false;
		while ($row3 = mysqli_fetch_assoc($result2))
		{
			if ($row3['user'] == $user2)
			{
				$liked = true;
				break;
			}
		}
	}
	$postData = array('imageFilename' => $filename, 'profileFilename' => $filename2, 'image' => $image, 'id' => $row['id'], 'user' => $row['user'], 'timePosted' => $row['timePosted'], 'caption' => $row['caption'], 'username' => $row2['username'], 'likes' => mysqli_num_rows($result2) - 1, 'liked' => $liked, 'comments' => mysqli_num_rows($result), 'profilePicture' => $profilePicture);	
	header( 'Content-type: application/json; charset=utf-8' );
	$jsonStr = json_encode($postData);
	echo $jsonStr;
} 
else 
{
    echo "Shit Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
