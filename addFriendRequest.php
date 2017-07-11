<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$requester = $_GET['user1'];
$receiver = $_GET['user2'];
if ($requester == $receiver)
{
	die("Can't friend yourself!");
}
$sql = "SELECT id, requester, receiver FROM friends";
$result = mysqli_query($conn, $sql);
if ($result)
{
	$idFound = false;
	$request = mysqli_num_rows($result) + 1;
    for ($i = 1; $row = mysqli_fetch_assoc($result); $i++)
    {
		if (!$idFound && $i < $row['id'])
		{
			$request = $i;
			$idFound = true;
		}
		if ($row["requester"] == $requester && $row['receiver'] == $receiver)
		{
	    	die("Request already exists");
		}
	}
}
else
{
	echo "Error: $sql <br>" . mysqli_error($conn);
}

$sql = "INSERT INTO friends (id, requester, receiver)
         VALUES ($request, $requester, $receiver)";
if (mysqli_query($conn, $sql))
{
	echo "New record created successfully";
}
else
{
	die("Error: $sql <br>" . mysqli_error($conn));
}
$sql = "SELECT deviceToken FROM users WHERE id = $receiver";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$deviceToken = $row["deviceToken"];
$sql = "SELECT username FROM users WHERE id = $requester";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$username = $row["username"];
echo "\n$deviceToken\n$username";
$payload['aps'] = array('alert' => "$username sent you an enemy request!", 'badge' => 1, 'sound' => 'default');
$payload['userID'] = $requester;
$payload = json_encode($payload);
$apnsHost = 'gateway.push.apple.com';
$apnsPort = 2195;
$apnsCert = 'pushcert.pem';
$streamContext = stream_context_create();
stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
echo $errorString;
$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
fwrite($apns, $apnsMessage);
socket_close($apns);
fclose($apns);
mysqli_close($conn);
?>
