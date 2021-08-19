<?php

$t = time();

$data = $_POST['u'];
$data = substr($data, strpos($data, ",") + 1);
$data = base64_decode($data);
$file = "chart/" . $t . ".jpg";
file_put_contents($file, $data);

echo $file;
