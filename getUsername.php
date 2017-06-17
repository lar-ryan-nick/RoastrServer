<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
if (!$conn)
{
    die("Connection Failure: " . mysqli_connect_error());
}

$id = $_GET['arg1'];

$sql = "SELECT user FROM posts WHERE id = $id";
$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result))
{
    $id2 = $row['user'];
    $sql2 = "SELECT username FROM users WHERE id = $id2";
    $result2 = mysqli_query($conn, $sql2);

    if ($row2 = mysqli_fetch_assoc($result2))
    {
    	echo $row2['username'];
    }
}

mysqli_close($conn);
?>
