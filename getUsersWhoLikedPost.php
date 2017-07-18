<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$post = $_GET['arg1'];

$sql = "SELECT * FROM likes WHERE post = $post";
$result = pg_query($conn, $sql);

if (pg_num_rows($result) >= 0)
{
    $likeUsers = array();
    for ($i = 0; $row = pg_fetch_array($result); $i++)
    {
		$x = $row['user'];
        $sql2 = "SELECT username FROM users WHERE id = $x";
        $result2 = pg_query($conn, $sql2);
        if ($row2 = pg_fetch_array($result2))
            $likeUsers[$row['id']] = array('user' => $row['user'], 'timeLiked' => $row['timeLiked'], 'username' => $row2['username'], 'post' => $row['post']);
    }
    echo json_encode($likeUsers);
}
else
{
    echo "Error: " . $sql . "<br>" . pg_last_error($conn);
}

pg_close($conn);
?>

