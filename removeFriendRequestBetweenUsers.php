<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user1 = $_GET['user1'];
$user2 = $_GET['user2'];

$sql1 = "DELETE FROM friends WHERE requester = $user1 AND receiver = $user2 OR receiver = $user1 AND requester = $user2";
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
