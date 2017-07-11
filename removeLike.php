<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = $_GET['user'];
$post = $_GET['post'];

$sql1 = "DELETE FROM likes WHERE user = $user AND post = $post";
if (mysqli_query($conn, $sql1))
{
	echo "Success!";
}
else
{
	echo "Error: $sql <br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
