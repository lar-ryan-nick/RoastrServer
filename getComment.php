<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$post = $_GET['arg1'];
$index = $_GET['arg2'];

$sql = "SELECT id, user, comment FROM comments WHERE post = $post";
$result = mysqli_query($conn, $sql);
//echo "poop";
if (mysqli_num_rows($result) > 0)
{
    for ($i = 0; $i < $index; $i++)
    {
		$row = mysqli_fetch_assoc($result);
    }
	$x = $row['user'];
	$sql2 = "SELECT username FROM users WHERE id = $x";
	$result2 = mysqli_query($conn, $sql2);
	if ($row2 = mysqli_fetch_assoc($result2))
	{
		$likeUsers = array($row['id'] => $row2['username'] . " " . $row['comment']);
		echo json_encode($likeUsers);
	}
	else
	{
		echo "Error: " . $sql . "<br>" . mysqli_error($conn);
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
