<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$index = $_GET['arg1'];

$sql = "SELECT * FROM posts";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0)
{
    for ($i = 0; $i < $index; $i++)
    {
        $row = mysqli_fetch_assoc($result);
    }
    echo $row["id"];
}
else if (mysqli_num_rows($result) == 0)
{
    echo "No post";
}
else
{
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
