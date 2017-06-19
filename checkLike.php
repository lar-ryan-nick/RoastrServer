<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = $_GET['arg1'];
$post = $_GET['arg2'];

$sql1 = "SELECT * FROM likes WHERE post = $post";
$result = mysqli_query($conn, $sql1);

if (mysqli_num_rows($result) > 0)
{
    while($row = mysqli_fetch_assoc($result))
    {
        if ($row["user"] == $user)
        {
            die("Like already exists");
        }
    }
}
echo "Hasn't liked";

mysqli_close($conn);
?>
