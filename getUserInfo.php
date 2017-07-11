<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = $_GET['arg1'];

$sql = "SELECT id, adsRemoved FROM users WHERE username = $user";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) 
{
    if ($row = mysqli_fetch_assoc($result)) 
    {
        $data = array('id' => $row["id"], 'adsRemoved' => $row['adsRemoved']);
		echo json_encode($data);
    }
} 
else 
{
    echo "user does not exist";
}

mysqli_close($conn);
?>
