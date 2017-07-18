<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$id = $_GET['arg1'];

$sql = "SELECT image FROM posts WHERE id = $id";
$result = pg_query($conn, $sql);
if ($row = pg_fetch_array($result))
{
    $filename = $row["image"];
	$file = fopen("images/$filename", "r") or die("Unable to open file");
	//echo $filename . "<br>";
	$data = fread($file, filesize("images/$filename"));
	//echo "Data: $data <br>";
	$image = base64_encode($data);
	echo $image;
}

pg_close($conn);
?>
