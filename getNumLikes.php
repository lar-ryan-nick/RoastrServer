<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$post = $_GET['arg1'];

$sql = "SELECT * FROM likes WHERE post = $post";
$result = mysqli_query($conn, $sql);

echo mysqli_num_rows($result) - 1;

mysqli_close($conn);
?>