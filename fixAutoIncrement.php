<?php
	$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
	if ($conn)
	{
		echo "Lesssssss Goooooooooo";
	}
	else
	{
		echo "Fuck";
	}
	$sql = "ALTER SEQUENCE comments_id_seq RESTART WITH 444";
	pg_query($conn, $sql);
	pg_close($conn);
?>
