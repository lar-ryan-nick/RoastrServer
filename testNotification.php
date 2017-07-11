<?php
$conn = mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
if (!$conn)
{
    die("Connection Failure: " . mysqli_connect_error());
}

$id = $_GET['arg1'];
//echo "poop";
$sql = "SELECT deviceToken FROM users WHERE id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$deviceToken = $row["deviceToken"];
mysqli_close($conn);
$payload['aps'] = array('alert' => 'This is the alert text', 'badge' => 1, 'sound' => 'default');
$payload = json_encode($payload);
$apnsHost = 'gateway.push.apple.com';
$apnsPort = 2195;
$apnsCert = 'pushcert.pem';
//echo "$deviceToken";
$streamContext = stream_context_create();
stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);

$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
//if ($error)
    echo $errorString;
$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
fwrite($apns, $apnsMessage);
socket_close($apns);
fclose($apns);
?>
