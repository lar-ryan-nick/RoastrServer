<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$post = $_GET['arg1'];
$index = $_GET['arg2'];

$sql = "SELECT * FROM likes WHERE post = $post ORDER BY timeLiked";
$result = pg_query($conn, $sql);

if (pg_num_rows($result) >= 0)
{
    for ($i = 0; $i < $index && $row = pg_fetch_array($result); $i++)
    {
		if ($row['user'] == 0)
		{
			$i--;
		}
	}
        $sql = "SELECT username, profilePicture FROM users WHERE id = " . $row['user'];
        $result = pg_query($conn, $sql);
        if ($row2 = pg_fetch_array($result))
		{
			$filename = $row2['profilePicture'];
			$file = fopen("profilePictures/$filename", "r");
			$data = fread($file, filesize("profilePictures/$filename"));
			$picture = base64_encode($data);
            $likeData = array('id' => $row['id'], 'user' => $row['user'], 'timeLiked' => $row['timeLiked'], 'username' => $row2['username'], 'post' => $row['post'], 'profilePicture' => $picture);
		}
    echo json_encode($likeData);
}
else
{
    echo "Error: " . $sql . "<br>" . pg_last_error($conn);
}

pg_close($conn);
?>

