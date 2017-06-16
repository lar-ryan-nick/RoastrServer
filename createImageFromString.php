<?php
/*
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
*/
$image = $_REQUEST['image'];
$caption = $_REQUEST['arg1'];
$user = $_REQUEST['arg2'];
if (!$image)
{
	die("image not found!");
}
$image = base64_decode($image);
$file = fopen("images/image.jpg", "w") or die("Unable to open file!");
fwrite($file, $image);
fclose($file);

//mysqli_close($conn);
?>
