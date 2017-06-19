<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$comment = $_GET['arg1'];

$sql = "DELETE FROM comments WHERE id = $comment";
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
