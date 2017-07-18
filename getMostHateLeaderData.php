<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$index = $_GET['arg1'];

$sql = "SELECT id FROM users";
$result3 = pg_query($conn, $sql);
$likes = array();
while ($row3 = pg_fetch_array($result3))
{
$likes[$row3['id']] = 0;
$sql = "SELECT id FROM posts WHERE \"user\" = " . $row3['id'];
if ($result = pg_query($conn, $sql)) 
{
	while ($row = pg_fetch_array($result))
    {
		$sql = "SELECT id FROM likes WHERE post = " . $row['id'];
		$result2 = pg_query($conn, $sql);
		$likes[$row3['id']] += pg_num_rows($result2) - 1;
	}
}
else
{
    echo "Error: " . $sql . "<br>" . pg_last_error($conn);
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
		$sql = "SELECT username, profilepicture FROM users WHERE id = " . $largest;
    	$result2 = pg_query($conn, $sql);
    	if ($row2 = pg_fetch_array($result2))
    	{
			$filename = $row2['profilepicture'];
	    	$file = fopen("profilePictures/$filename", "r");
	    	$data = fread($file, filesize("profilePictures/$filename"));
	    	fclose($file);
    		$picture = base64_encode($data);
			$userData = array('id' => $largest, 'username' => $row2['username'], 'likes' => $likes[$largest], 'profilePicture' => $picture);
			echo json_encode($userData);
		}
		else
    	{
        	echo "Error: " . $sql . "<br>" . pg_last_error($conn);
    	}

pg_close($conn);
?>
