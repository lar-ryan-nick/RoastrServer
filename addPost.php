<?php
$conn =mysqli_connect("roastr.cwskii6ncohr.us-west-2.rds.amazonaws.com", "root", "Patrick4", "roastr", "3306");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$image = $_REQUEST['image'];
$caption = $_REQUEST['caption'];
$user = $_REQUEST['userID'];

$sql = "SELECT id FROM posts";
$result = mysqli_query($conn, $sql);
$post = mysqli_num_rows($result) + 1;
for ($i = 1; $row = mysqli_fetch_assoc($result); $i++)
{
	if ($i < $row['id'])
	{
		$post = $i;
		break;
	}
}

if ($image == "")
{
    $sql = "INSERT INTO posts (id, caption, user)
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
	$sql = "INSERT INTO posts (id, image, caption, user)
            VALUES ($post, \"$name\", " . chop($caption) . ", $user)";   
}   
if (mysqli_query($conn, $sql)) 
{
    echo "New record created successfully <br>";
	$sql = "SELECT id, user, post FROM likes";
	$result = mysqli_query($conn, $sql);
	if ($result)
	{
	    $like = mysqli_num_rows($result) + 1;
	    for ($i = 1; $row = mysqli_fetch_assoc($result); $i++)
		{   
   	    	if ($i < $row['id'])
   	    	{   
   	        	$like = $i; 
            	break;
        	}   
		}   
		$sql = "INSERT INTO likes (id, user, post)
				VALUES ($like, 0, $post)";
		mysqli_query($conn, $sql);
    }   
} 
else
{
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
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
	$result = mysqli_query($conn, $sql);
	if ($row = mysqli_fetch_assoc($result))
    {   
    	$deviceTokens[] = $row["deviceToken"];
    }
    $index++;
}
echo json_encode($deviceTokens) . " <br>";
if ($deviceTokens)
{
	$sql = "SELECT username FROM users WHERE id = $user";
	$result = mysqli_query($conn, $sql);
	if ($row = mysqli_fetch_assoc($result))
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
mysqli_close($conn);
?>