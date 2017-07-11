<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$post = $_GET['arg1'];
$index = $_GET['arg2'];

$sql = "SELECT * FROM likes WHERE post = $post ORDER BY timeLiked";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) >= 0)
{
    for ($i = 0; $i < $index && $row = mysqli_fetch_assoc($result); $i++)
    {
		if ($row['user'] == 0)
		{
			$i--;
		}
	}
        $sql = "SELECT username, profilePicture FROM users WHERE id = " . $row['user'];
        $result = mysqli_query($conn, $sql);
        if ($row2 = mysqli_fetch_assoc($result))
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
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>

