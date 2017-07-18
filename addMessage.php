<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$message = $_REQUEST['message'];
$sender = $_REQUEST['sender'];
$receiver = $_REQUEST['receiver'];

if ($sender == 0 || $receiver == 0)
{
	die("Message failed to send");
}

$sql = "SELECT id FROM messages";
$result = pg_query($conn, $sql);
if ($result)
{
    $id = pg_num_rows($result) + 1;
    for ($i = 1; $row = pg_fetch_array($result); $i++)
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
    echo "Error: $sql <br>" . pg_last_error($conn);
}	

$sql = "INSERT INTO messages (id, message, sender, receiver)
        VALUES ($id, \"" . chop($message) . "\", $sender, $receiver)";
if (pg_query($conn, $sql))
{
    echo "New record created successfully <br>";
}
else
{
    echo "Error: " . $sql . "<br>" . pg_last_error($conn);
}
if ($sender != $receiver)
{   
    $sql = "SELECT username FROM users WHERE id = $sender";
	$result = pg_query($conn, $sql);
	if ($row = pg_fetch_array($result))
	{
    	$username = $row['username'];
	}
	$sql = "SELECT deviceToken FROM users WHERE id = $receiver";
    $result = pg_query($conn, $sql);
    if ($row = pg_fetch_array($result))
    {   
        $deviceToken = $row['devicetoken'];
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
pg_close($conn);
?>
