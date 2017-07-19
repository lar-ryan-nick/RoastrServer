<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$username = $_GET['username'];
$pass = $_GET['password'];

$sql = "SELECT id, username FROM users ORDER BY id";
$result = pg_query($conn, $sql);

if ($result)
{
	$idFound = false;
	$user = pg_num_rows($result) + 1;
    for ($i = 1; $row = pg_fetch_array($result); $i++)
    {   
        if (!$idFound && $i < $row['id'])
        {   
			echo "$i " . $row['id']; 
            $user = $i; 
            $idFound = true;
        }   
        if ($row["username"] == $username)
        {   
            die("User already exists");
        }   
    }   
}
else
{
	echo "Error: " . $sql . "<br>" . pg_last_error($conn);
}

$sql = "INSERT INTO users (id, username, password)
        VALUES ($user, '$username', '$pass')";
if (pg_query($conn, $sql)) 
{
	echo "New record created successfully";
} 
else
{
	echo "Error: " . $sql . "<br>" . pg_last_error($conn);
}

pg_close($conn);
?>
