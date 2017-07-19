<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$user = $_GET['arg1'];
$index = $_GET['arg2'];

$sql = "SELECT DISTINCT sender, receiver FROM (SELECT DISTINCT receiver, sender, timesent FROM messages WHERE sender = $user UNION SELECT DISTINCT sender, receiver, timesent FROM messages WHERE receiver = $user ORDER BY timesent DESC) AS t";
$result = pg_query($conn, $sql);

if (pg_num_rows($result) >= 0)
{
    for ($i = 0; $i < $index && $row = pg_fetch_array($result); $i++)
    {
	}
		if ($row['sender'] != $user)
		{
			$other = $row['sender'];
		}
		else
		{
			$other = $row['receiver'];
		}
        $sql = "SELECT username, profilePicture FROM users WHERE id = " . $other;
        $result = pg_query($conn, $sql);
        if ($row = pg_fetch_array($result))
		{
			$filename = $row['profilepicture'];
			if ($filename == "")
			{
				$filename = "roastrtransparent.png";
			}
			$file = fopen("profilePictures/$filename", "r");
			$data = fread($file, filesize("profilePictures/$filename"));
			$picture = base64_encode($data);
		}
		$sql = "SELECT timesent, message FROM messages WHERE sender = $other AND receiver = $user OR sender = $user AND receiver = $other ORDER BY timesent DESC";
		$result = pg_query($conn, $sql);
		if ($row2 = pg_fetch_array($result))
		{
			$likeData = array('user' => $other, 'lastSent' => $row2['timesent'], 'username' => $row['username'], 'message' => $row2['message'], 'profilePicture' => $picture, 'profileFilename' => $filename);
		}
    echo json_encode($likeData);
}
else
{
    echo "Error: " . $sql . "<br>" . pg_last_error($conn);
}
pg_close($conn);
?>
