<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = $_GET['arg1'];

$sql = "SELECT * FROM posts WHERE user = $user";
$result = mysqli_query($conn, $sql);

echo mysqli_num_rows($result);

mysqli_close($conn);
?>
