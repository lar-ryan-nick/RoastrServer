<?php
$conn =mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = $_REQUEST['message'];
$sender = $_REQUEST['sender'];
$receiver = $_REQUEST['receiver'];

if ($sender == 0 || $receiver == 0)
{
	die("Message failed to send");
}

$sql = "SELECT id FROM messages";
$result = mysqli_query($conn, $sql);
if ($result)
{
    $id = mysqli_num_rows($result) + 1;
    for ($i = 1; $row = mysqli_fetch_assoc($result); $i++)
    {   
        if ($i < $row['id'])
        {   
            $id = $i; 
            break;
        }   
    }
}
else
{
    echo "Error: $sql <br>" . mysqli_error($conn);
}	

$sql = "INSERT INTO messages (id, message, sender, receiver)
        VALUES ($id, \"" . chop($message) . "\", $sender, $receiver)";
if (mysqli_query($conn, $sql))
{
    echo "New record created successfully <br>";
}
else
{
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
if ($sender != $receiver)
{   
    $sql = "SELECT username FROM users WHERE id = $sender";
	$result = mysqli_query($conn, $sql);
	if ($row = mysqli_fetch_assoc($result))
	{
    	$username = $row['username'];
	}
	$sql = "SELECT deviceToken FROM users WHERE id = $receiver";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result))
    {   
        $deviceToken = $row['deviceToken'];
    }
	$body['aps'] = array('alert' => "$username: $message", 'badge' => 1, 'sound' => 'default');
	$body['sender'] = $sender;
	$payload = json_encode($body);
	$apnsHost = 'gateway.sandbox.push.apple.com';
	$apnsPort = 2195;
	$apnsCert = 'apns-dev.pem';
	$streamContext = stream_context_create();
	stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
	$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
	echo $errorString;
	echo $deviceToken;
    $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
    fwrite($apns, $apnsMessage); 
	//$body['aps'] = array('alert' => "$username mentioned you in a roast!", 'badge' => 1, 'sound' => 'default');
	//$payload = json_encode($body);
	socket_close($apns);
	fclose($apns);
	$apnsHost = 'gateway.push.apple.com';
	$apnsPort = 2195;
	$apnsCert = 'pushcert.pem';
	$streamContext = stream_context_create();
	stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
	$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
	echo $errorString;
	echo $deviceToken;
    $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
    fwrite($apns, $apnsMessage); 
	//$body['aps'] = array('alert' => "$username mentioned you in a roast!", 'badge' => 1, 'sound' => 'default');
	//$payload = json_encode($body);
	socket_close($apns);
	fclose($apns);

}
mysqli_close($conn);
?>
