<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$post = $_GET['arg1'];

$sql = "SELECT image FROM posts WHERE id = $post";
$result = mysqli_query($conn, $sql);
if ($result)
{
	$row = mysqli_fetch_assoc($result);
	unlink("images/" . $row['image']);
}
else
{
	echo "Error: $sql <br>" . mysqli_error($conn);
}

$sql = "DELETE FROM posts WHERE id = $post";
mysqli_query($conn, $sql);

$sql = "DELETE FROM comments WHERE post = $post";
mysqli_query($conn, $sql);

$sql = "DELETE FROM likes WHERE post = $post";
mysqli_query($conn, $sql);

mysqli_close($conn);
?>
