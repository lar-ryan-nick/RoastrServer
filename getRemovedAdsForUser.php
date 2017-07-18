<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$user = $_GET['arg1'];

$sql1 = "SELECT adsRemoved FROM users WHERE id = $user";
$result = pg_query($conn, $sql1);

if (pg_num_rows($result) > 0) 
{
    while($row = pg_fetch_array($result)) 
    {
        echo $row["adsRemoved"];
    }
} 
else 
{
    echo "user does not exist";
}

pg_close($conn);
?>
