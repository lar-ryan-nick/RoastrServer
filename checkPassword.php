<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = $_REQUEST['arg1'];
$pass = $_REQUEST['arg2'];

$sql1 = "SELECT * FROM users WHERE username = $user";
$result = mysqli_query($conn, $sql1);

if (mysqli_num_rows($result) > 0) 
{
    $row = mysqli_fetch_assoc($result);
    /*
    echo $row['password'] . "\n";
    echo $pass . "\n";
    echo strcmp($row['password'], $pass) . "\n";
    */
    if (strcmp($row['password'], $pass) == 0)
        echo "Password is correct";
    else
	echo "Password isn't correct";
} 
else 
{
    echo "User doesn't exist";
}

mysqli_close($conn);
?>
