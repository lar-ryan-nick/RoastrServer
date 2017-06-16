<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$picture = $_POST['picture'];
$user = $_POST['userID'];
echo $picture;

$sql = "SELECT profilePicture FROM users WHERE id = $user";
$result = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($result))
{
	$filename = $row['profilePicture'];
	unlink("profilePictures/$filename");
} 
$currentDate = date("Y-m-d");
$name  = "" . $currentDate . microtime() . rand(0, 999) . rand(0, 999) . rand(0, 999) . ".jpg";
//echo "$name <br>";
$file = fopen("profilePictures/$name", "w") or die("Unable to open file");
$picture = base64_decode($picture);
fwrite($file, $picture);
fclose($file);
$sql = "UPDATE users SET profilePicture = \"$name\" WHERE id = $user";
if (mysqli_query($conn, $sql)) 
{
    //echo "Success!";
} 
else
{
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
