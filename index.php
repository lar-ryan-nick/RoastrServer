<?php
$conn = mysqli_connect("Enter database info here");
If ($conn)
{
	echo "Hooray!";
}
else
{
	die("Connection failed: " . mysqli_connect_error());
}
mysqli_close($conn);
?>
