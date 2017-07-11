<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = $_GET['arg1'];
$post = $_GET['arg2'];

$sql = "SELECT caption FROM posts WHERE user = $user";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) 
{
    for ($i = 0; $i < $post; $i++)
    {
        $row = mysqli_fetch_assoc($result);
    }
    echo $row["caption"];
} 
else if (mysqli_num_rows($result) == 0)
{
    echo "No captions";
}
else 
{
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
