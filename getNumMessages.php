<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user1 = $_GET['user1'];
$user2 = $_GET['user2'];

$sql = "SELECT id FROM messages WHERE sender = $user1 AND receiver = $user2 OR sender = $user2 AND receiver = $user1";
$result = mysqli_query($conn, $sql);

echo mysqli_num_rows($result);

mysqli_close($conn);
?>
