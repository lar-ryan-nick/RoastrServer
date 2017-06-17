<?php
header('Content-type: image/jpg');
$file = fopen("images/image.png", "r");
echo fread($file);
?>
