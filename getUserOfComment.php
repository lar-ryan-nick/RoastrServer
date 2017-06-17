<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$post = $_GET['arg1'];
$index = $_GET['arg2'];

$sql = "SELECT * FROM comments WHERE post = $post";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0)
{
    for ($i = 0; $i < $index; $i++)
    {
        $row = mysqli_fetch_assoc($result);
    }
    $id = $row["user"];

    $sql2 = "SELECT * FROM users WHERE id = $id";
    $result2 = mysqli_query($conn, $sql2);

    if ($row2 = mysqli_fetch_assoc($result2))
    {
	echo $row2["username"];
    }
}
else if (mysqli_num_rows($result) == 0)
{
    echo "No comments";
}
else
{
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
