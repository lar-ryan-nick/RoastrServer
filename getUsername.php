<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
if (!$conn)
{
    die("Connection Failure: " . pg_last_error());
}

$id = $_GET['arg1'];

$sql = "SELECT user FROM posts WHERE id = $id";
$result = pg_query($conn, $sql);

if ($row = pg_fetch_array($result))
{
    $id2 = $row['user'];
    $sql2 = "SELECT username FROM users WHERE id = $id2";
    $result2 = pg_query($conn, $sql2);

    if ($row2 = pg_fetch_array($result2))
    {
    	echo $row2['username'];
    }
}

pg_close($conn);
?>
