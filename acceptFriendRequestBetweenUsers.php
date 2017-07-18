<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$requester = $_GET['user1'];
$receiver = $_GET['user2'];

$sql = "UPDATE friends SET accepted = 1 WHERE requester = $requester AND receiver = $receiver";
if (pg_query($conn, $sql)) 
{
    echo "Success!";
} 
else
{
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
$sql = "SELECT deviceToken FROM users WHERE id = $requester";
$result = pg_query($conn, $sql);
$row = pg_fetch_array($result);
$deviceToken = $row["deviceToken"];
$sql = "SELECT username FROM users WHERE id = $receiver";
$result = pg_query($conn, $sql);
$row = pg_fetch_array($result);
$username = $row["username"];
echo "\n$deviceToken\n$username";
$payload['aps'] = array('alert' => "$username accepted your enemy request!", 'badge' => 1, 'sound' => 'default');
$payload['userID'] = $receiver;
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
pg_close($conn);
?>
