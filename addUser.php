<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$username = $_GET['username'];
$pass = $_GET['password'];

$sql = "SELECT id, username FROM users ORDER BY id";
$result = mysqli_query($conn, $sql);

if ($result)
{
	$idFound = false;
	$user = mysqli_num_rows($result) + 1;
    for ($i = 1; $row = mysqli_fetch_assoc($result); $i++)
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
	echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

$sql = "INSERT INTO users (id, username, password)
        VALUES ($user, $username, $pass)";
if (mysqli_query($conn, $sql)) 
{
	echo "New record created successfully";
} 
else
{
	echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
