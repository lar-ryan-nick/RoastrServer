<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = $_GET['user'];
$post = $_GET['post'];

$sql = "SELECT id, user, post FROM likes";
$result = mysqli_query($conn, $sql);
if ($result)
{
	$idFound = false;
	$like = mysqli_num_rows($result) + 1;
    for ($i = 1; $row = mysqli_fetch_assoc($result); $i++)
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
	echo "Error: $sql <br>" . mysqli_error($conn);
}

$sql = "INSERT INTO likes (id, user, post)
         VALUES ($like, $user, $post)";
if (mysqli_query($conn, $sql))
{
	echo "New record created successfully";
}
else
{
	die("Error: $sql <br>" . mysqli_error($conn));
}
$sql = "SELECT user FROM posts WHERE id = $post";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$id = $row['user'];
if ($id != $user)
{
    $sql = "SELECT deviceToken FROM users WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $deviceToken = $row["deviceToken"];
    $sql = "SELECT username FROM users WHERE id = $user";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
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
mysqli_close($conn);
?>