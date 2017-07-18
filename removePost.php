<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}
$post = $_GET['arg1'];

$sql = "SELECT image FROM posts WHERE id = $post";
$result = pg_query($conn, $sql);
if ($result)
{
	$row = pg_fetch_array($result);
	unlink("images/" . $row['image']);
}
else
{
	echo "Error: $sql <br>" . pg_last_error($conn);
}

$sql = "DELETE FROM posts WHERE id = $post";
pg_query($conn, $sql);

$sql = "DELETE FROM comments WHERE post = $post";
pg_query($conn, $sql);

$sql = "DELETE FROM likes WHERE post = $post";
pg_query($conn, $sql);

pg_close($conn);
?>
