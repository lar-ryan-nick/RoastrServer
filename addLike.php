<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$user = $_GET['user'];
$post = $_GET['post'];

$sql = "SELECT * FROM likes";
$result = pg_query($conn, $sql);
if ($result)
{
	$idFound = false;
	$like = pg_num_rows($result) + 1;
    for ($i = 1; $row = pg_fetch_array($result); $i++)
    {
		if (!$idFound && $i < $row['id'])
		{
			$like = $i;
			$idFound = true;
		}
		if ($row["post"] == $post && $row['user'] == $user)
		{
	    	die("Like already exists");
		}
	}
}
else
{
	echo "Error: $sql <br>" . pg_last_error($conn);
}

$sql = "INSERT INTO likes (id, \"user\", post)
         VALUES ($like, $user, $post)";

if (pg_query($conn, $sql))
{
	echo "New record created successfully";
}
else
{
	die("Error: $sql <br>" . pg_last_error($conn));
}
$sql = "SELECT user FROM posts WHERE id = $post";
$result = pg_query($conn, $sql);
$row = pg_fetch_array($result);
$id = $row['user'];
if ($id != $user)
{
    $sql = "SELECT deviceToken FROM users WHERE id = $id";
    $result = pg_query($conn, $sql);
    $row = pg_fetch_array($result);
    $deviceToken = $row["deviceToken"];
    $sql = "SELECT username FROM users WHERE id = $user";
    $result = pg_query($conn, $sql);
    $row = pg_fetch_array($result);
    $username = $row["username"];
    echo "\n$deviceToken\n$username";
    $payload['aps'] = array('alert' => "$username hated one of your posts!", 'badge' => 1, 'sound' => 'default');
    $payload['postID'] = $post;
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
    $apnsHost = 'gateway.sandbox.push.apple.com';
    $apnsPort = 2195;
    $apnsCert = 'apns-dev.pem';
    $streamContext = stream_context_create();
    stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
    $apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
    echo $errorString;
    $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
    fwrite($apns, $apnsMessage);
    socket_close($apns);
    fclose($apns);
}
pg_close($conn);
?>
