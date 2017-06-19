<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$id = $_GET['arg1'];

$sql = "SELECT image FROM posts WHERE id = $id";
$result = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($result))
{
    $filename = $row["image"];
	$file = fopen("images/$filename", "r") or die("Unable to open file");
	//echo $filename . "<br>";
	$data = fread($file, filesize("images/$filename"));
	//echo "Data: $data <br>";
	$image = base64_encode($data);
	echo $image;
}

mysqli_close($conn);
?>
