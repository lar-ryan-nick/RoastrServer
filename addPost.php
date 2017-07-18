<?php
$conn = pg_connect("host=ec2-107-21-205-25.compute-1.amazonaws.com port=5432 dbname=d4v9qcehkq46dq user=lbzclfrlzbwlmj password=2b4aa87b7fa7b7b4761c1e57e540836210b309d95b08853e09ce6158ada6bab9");
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$image = $_REQUEST['image'];
$caption = $_REQUEST['caption'];
$user = $_REQUEST['userID'];

$sql = "SELECT id FROM posts";
$result = pg_query($conn, $sql);
$post = pg_num_rows($result) + 1;
for ($i = 1; $row = pg_fetch_array($result); $i++)
{
	if ($i < $row['id'])
	{
		$post = $i;
		break;
	}
}

if ($image == "")
{
    $sql = "INSERT INTO posts (id, caption, \"user\")
            VALUES ($post, " . chop($caption) . ", $user)";
}
else
{
	$currentDate = date("Y-m-d");
	$name  = "" . $currentDate . microtime() . rand(0, 999) . rand(0, 999) . rand(0, 999) . ".jpg";
	echo "$name <br>";
	$file = fopen("images/$name", "w") or die("Unable to open file");
	$image = base64_decode($image);
	fwrite($file, $image);
	fclose($file);
	$sql = "INSERT INTO posts (id, image, caption, \"user\")
            VALUES ($post, \"$name\", " . chop($caption) . ", $user)";   
}   
if (pg_query($conn, $sql)) 
{
    echo "New record created successfully <br>";
	$sql = "SELECT * FROM likes";
	$result = pg_query($conn, $sql);
	if ($result)
	{
	    $like = pg_num_rows($result) + 1;
	    for ($i = 1; $row = pg_fetch_array($result); $i++)
		{   
   	    	if ($i < $row['id'])
   	    	{   
   	        	$like = $i; 
            	break;
        	}   
		}   
		$sql = "INSERT INTO likes (id, \"user\", post)
				VALUES ($like, 0, $post)";
		pg_query($conn, $sql);
    }   
} 
else
{
    echo "Error: " . $sql . "<br>" . pg_last_error($conn);
}
$index = 0;
$deviceTokens = array();
while ($index = strpos($caption, "@", $index))
{
	if ($x = strpos($caption, " ", $index))
	{
		$substring = substr($caption, $index + 1, $x - $index - 1);
	}
	else
	{
		$substring = substr($caption, $index + 1, strlen($caption) - $index - 2);
	}
	$sql = "SELECT deviceToken FROM users WHERE username = \"$substring\"";
	$result = pg_query($conn, $sql);
	if ($row = pg_fetch_array($result))
    {   
    	$deviceTokens[] = $row["deviceToken"];
    }
    $index++;
}
echo json_encode($deviceTokens) . " <br>";
if ($deviceTokens)
{
	$sql = "SELECT username FROM users WHERE id = $user";
	$result = pg_query($conn, $sql);
	if ($row = pg_fetch_array($result))
	{
		$username = $row['username'];
	}
	$payload['aps'] = array('alert' => "$username mentioned you in a post!", 'badge' => 1, 'sound' => 'default');
	$payload['postID'] = $post;
	$payload = json_encode($payload);
	$apnsHost = 'gateway.push.apple.com';
	$apnsPort = 2195;
	$apnsCert = 'pushcert.pem';
	$streamContext = stream_context_create();
	stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
	$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
	echo $errorString;
	foreach ($deviceTokens as $deviceToken)
	{
		echo $deviceToken . " <br>";
		$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
    	fwrite($apns, $apnsMessage);
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
	foreach ($deviceTokens as $deviceToken)
	{
		echo $deviceToken . " <br>";
		$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
    	fwrite($apns, $apnsMessage);
	}
	socket_close($apns);
	fclose($apns);
}
pg_close($conn);
?>
