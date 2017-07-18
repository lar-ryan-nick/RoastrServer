<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$picture = $_POST['picture'];
$user = $_POST['userID'];
echo $picture;

$sql = "SELECT profilePicture FROM users WHERE id = $user";
$result = pg_query($conn, $sql);
if ($row = pg_fetch_array($result))
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
if (pg_query($conn, $sql)) 
{
    //echo "Success!";
} 
else
{
    echo "Error: " . $sql . "<br>" . pg_last_error($conn);
}

pg_close($conn);
?>
