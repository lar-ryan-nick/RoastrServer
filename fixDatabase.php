<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

for ($i = 1; $i <= 24; $i++)
{
$sql = "SELECT id, user, post FROM likes";
$result = mysqli_query($conn, $sql);
if ($result)
{
	$like = mysqli_num_rows($result) + 1;
    for ($j = 1; $row = mysqli_fetch_assoc($result); $j++)
    {
		if ($j < $row['id'])
		{
			$like = $j;
			break;
		}
	}
}
else
{
	echo "Error: $sql <br>" . mysqli_error($conn);
}

$sql = "INSERT INTO likes (id, user, post)
         VALUES ($like, 0, $i)";
if (mysqli_query($conn, $sql))
{
	echo "New record created successfully";
}
else
{
	die("Error: $sql <br>" . mysqli_error($conn));
}
}
mysqli_close($conn);
?>
