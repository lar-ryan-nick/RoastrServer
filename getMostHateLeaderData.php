<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$index = $_GET['arg1'];

$sql = "SELECT id FROM users";
$result3 = mysqli_query($conn, $sql);
$likes = array();
while ($row3 = mysqli_fetch_assoc($result3))
{
$likes[$row3['id']] = 0;
$sql = "SELECT id FROM posts WHERE user = " . $row3['id'];
if ($result = mysqli_query($conn, $sql)) 
{
	while ($row = mysqli_fetch_assoc($result))
    {
		$sql = "SELECT id FROM likes WHERE post = " . $row['id'];
		$result2 = mysqli_query($conn, $sql);
		$likes[$row3['id']] += mysqli_num_rows($result2) - 1;
	}
}
else
{
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
}
	//echo json_encode($likes) . '<br>';
	$largest = 0;
	for ($i = 0; $i < $index; $i++)
	{
		$likes[$largest] = -1;
		$largest = 0;
		for ($j = 1; $j <= count($likes); $j++)
		{
			if ($likes[$j] > $likes[$largest])
			{
				$largest = $j;
			}
		}	
	}
		$sql = "SELECT username, profilePicture FROM users WHERE id = " . $largest;
    	$result2 = mysqli_query($conn, $sql);
    	if ($row2 = mysqli_fetch_assoc($result2))
    	{
			$filename = $row2['profilePicture'];
	    	$file = fopen("profilePictures/$filename", "r");
	    	$data = fread($file, filesize("profilePictures/$filename"));
	    	fclose($file);
    		$picture = base64_encode($data);
			$userData = array('id' => $largest, 'username' => $row2['username'], 'likes' => $likes[$largest], 'profilePicture' => $picture);
			echo json_encode($userData);
		}
		else
    	{
        	echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    	}

mysqli_close($conn);
?>
