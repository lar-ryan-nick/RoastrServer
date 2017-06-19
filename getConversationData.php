<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = $_GET['arg1'];
$index = $_GET['arg2'];

$sql = "SELECT DISTINCT sender, receiver FROM (SELECT DISTINCT receiver, sender, timeSent FROM messages WHERE sender = $user UNION SELECT DISTINCT sender, receiver, timeSent FROM messages WHERE receiver = $user ORDER BY timeSent DESC) AS t";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) >= 0)
{
    for ($i = 0; $i < $index && $row = mysqli_fetch_assoc($result); $i++)
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
        $result = mysqli_query($conn, $sql);
        if ($row = mysqli_fetch_assoc($result))
		{
			$filename = $row['profilePicture'];
			if ($filename == "")
			{
				$filename = "roastrtransparent.png";
			}
			$file = fopen("profilePictures/$filename", "r");
			$data = fread($file, filesize("profilePictures/$filename"));
			$picture = base64_encode($data);
		}
		$sql = "SELECT timeSent, message FROM messages WHERE sender = $other AND receiver = $user OR sender = $user AND receiver = $other ORDER BY timeSent DESC";
		$result = mysqli_query($conn, $sql);
		if ($row2 = mysqli_fetch_assoc($result))
		{
			$likeData = array('user' => $other, 'lastSent' => $row2['timeSent'], 'username' => $row['username'], 'message' => $row2['message'], 'profilePicture' => $picture, 'profileFilename' => $filename);
		}
    echo json_encode($likeData);
}
else
{
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>

