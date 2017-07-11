<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = $_GET['arg1'];

$sql1 = "SELECT * FROM users WHERE username = $user";
$result = mysqli_query($conn, $sql1);

if (!$result)
{
	echo "Error: $sql <br>" . mysqli_connect($conn);
}
else if (mysqli_num_rows($result) > 0) 
{
    echo "User already exists";
} 
else 
{
    echo "Username is available";
}

mysqli_close($conn);
?>
