<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = $_GET['arg1'];

$sql = "SELECT id FROM posts WHERE user = $user";
if ($result = mysqli_query($conn, $sql))
{
	echo mysqli_num_rows($result);
}
else
{
	echo "Fuck Error: $sql <br>" . mysqli_error($conn);
}
mysqli_close($conn);
