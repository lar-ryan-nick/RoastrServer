<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$user1 = $_GET['user1'];
$user2 = $_GET['user2'];

$sql = "SELECT id FROM messages WHERE sender = $user1 AND receiver = $user2 OR sender = $user2 AND receiver = $user1";
$result = pg_query($conn, $sql);

echo pg_num_rows($result);

pg_close($conn);
?>
