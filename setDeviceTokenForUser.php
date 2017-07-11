<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$token = $_GET['arg1'];
$user = $_GET['arg2'];

$sql = "UPDATE users SET deviceToken = $token WHERE id = $user";
if (mysqli_query($conn, $sql)) 
{
    echo "Success!";
} 
else
{
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
