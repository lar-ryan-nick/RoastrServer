<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$post = $_GET['arg1'];

$sql = "SELECT * FROM likes WHERE post = $post";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) >= 0)
{
    $likeUsers = array();
    for ($i = 0; $row = mysqli_fetch_assoc($result); $i++)
    {
		$x = $row['user'];
        $sql2 = "SELECT username FROM users WHERE id = $x";
        $result2 = mysqli_query($conn, $sql2);
        if ($row2 = mysqli_fetch_assoc($result2))
            $likeUsers[$row['id']] = array('user' => $row['user'], 'timeLiked' => $row['timeLiked'], 'username' => $row2['username'], 'post' => $row['post']);
    }
    echo json_encode($likeUsers);
}
else
{
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>

