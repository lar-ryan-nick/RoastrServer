<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$sql = "SELECT id FROM users";
$result = mysqli_query($conn, $sql);

echo mysqli_num_rows($result);

mysqli_close($conn);
?>
