<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = $_GET['arg1'];
$index = $_GET['arg2'];

$sql = "SELECT requester FROM friends WHERE receiver = $user AND accepted = 0";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) >= 0)
{
    for ($i = 0; $i < $index && $row = mysqli_fetch_assoc($result); $i++)
    {
	}
	$other = $row['requester'];
    $sql = "SELECT username, profilePicture FROM users WHERE id = " . $other;
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result))
	{
		$filename = $row['profilePicture'];
		$file = fopen("profilePictures/$filename", "r");
		$data = fread($file, filesize("profilePictures/$filename"));
		$picture = base64_encode($data);
	}
	$likeData = array('user' => $other, 'username' => $row['username'], 'profilePicture' => $picture);
    echo json_encode($likeData);
}
else
{
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>

