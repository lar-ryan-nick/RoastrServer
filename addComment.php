<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$comment = $_REQUEST['comment'];
$post = $_REQUEST['post'];
$user = $_REQUEST['user'];

$sql = "SELECT id FROM comments";
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

$comment = addslashes($comment);

$sql = "INSERT INTO comments (id, comment, post, \"user\")
        VALUES ($id, '$comment', $post, $user);";
if (pg_query($conn, $sql))
{
    echo "New record created successfully <br>";
}
else
{
    echo "Error: " . $sql . "<br>" . pg_last_error($conn);
}
$sql2 = "SELECT \"user\" FROM posts WHERE id = $post";
$result2 = pg_query($conn, $sql2);
if ($row2 = pg_fetch_array($result2))
{
    $postUser = $row2['user'];
}
$sql3 = "SELECT * FROM users WHERE id = $user";
$result3 = pg_query($conn, $sql3);
if ($row3 = pg_fetch_array($result3))
{
	$username = $row3['username'];
}
$deviceTokens = array();
if ($postUser != $user)
{   
    $sql3 = "SELECT * FROM users WHERE id = $postUser";
    $result3 = pg_query($conn, $sql3);
    if ($row3 = pg_fetch_array($result3))
    {   
        $deviceTokens[] = $row3['devicetoken'];
    }
	$body['aps'] = array('alert' => "$username roasted one of your posts!", 'badge' => 1, 'sound' => 'default');
	$body['postID'] = $post;
	$payload = json_encode($body);
}
$index = 0;
while ($index = strpos($comment, "@", $index))
{
	if ($x = strpos($comment, " ", $index))
	{
		$substring = substr($comment, $index + 1, $x - $index - 1);
	}
	else
	{
		$substring = substr($comment, $index + 1, strlen($comment) - $index - 2);
	}
    $sql5 = "SELECT * FROM users WHERE username = \"$substring\"";
    $result5 = pg_query($conn, $sql5);
    if ($row5 = pg_fetch_array($result5))
    {
    	if ($row5['id'] != $user)
    	{
    		$deviceTokens[] = $row5["devicetoken"];
		}
	}
	$index++;
}
if ($payload)
{  
/*
	$body['aps'] = array('alert' => "$username mentioned you in a roast!", 'badge' => 1, 'sound' => 'default');
	$body['postID'] = $post;
	$payload = json_encode($body);
*/
}
else
{
    $body['aps'] = array('alert' => "$username mentioned you in a roast!", 'badge' => 1, 'sound' => 'default');
    $body['postID'] = $post;
    $payload = json_encode($body);
}
$apnsHost = 'gateway.push.apple.com';
$apnsPort = 2195;
$apnsCert = 'pushcert.pem';
$streamContext = stream_context_create();
stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
echo $errorString;
echo json_encode($deviceTokens);
foreach ($deviceTokens as $deviceToken)
{
	echo $payload;
    $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
    fwrite($apns, $apnsMessage); 
	$body['aps'] = array('alert' => "$username mentioned you in a roast!", 'badge' => 1, 'sound' => 'default');
	$payload = json_encode($body);
}
socket_close($apns);
fclose($apns);
$apnsHost = 'gateway.sandbox.push.apple.com';
$apnsPort = 2195;
$apnsCert = 'apns-dev.pem';
$streamContext = stream_context_create();
stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
echo $errorString;
echo json_encode($deviceTokens);
foreach ($deviceTokens as $deviceToken)
{
	echo $payload;
    $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
    fwrite($apns, $apnsMessage); 
	$body['aps'] = array('alert' => "$username mentioned you in a roast!", 'badge' => 1, 'sound' => 'default');
	$payload = json_encode($body);
}
socket_close($apns);
fclose($apns);
pg_close($conn);
?>
