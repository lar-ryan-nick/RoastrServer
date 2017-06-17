<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = $_GET['arg1'];
$sql = "SELECT username FROM users WHERE username LIKE '%" . $user . "%'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) >= 0) 
{
    if ($row = mysqli_fetch_assoc($result))
        $likeUsers = array($row['username'] => 0);
    else
        $likeUsers = array('No results found' => 0);
    while ($row = mysqli_fetch_assoc($result)) 
    {
        $likeUsers[$row['username']] = 0;
    }
    echo json_encode($likeUsers);
}
else 
{
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
