<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = $_GET['arg1'];

$sql = "DELETE FROM messages WHERE id = $message";
if (mysqli_query($conn, $sql))
{
    echo "Success";
}
else
{
    echo "Error: $sql <br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
