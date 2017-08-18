<?php
	$file  = 'data.json';
	$id    = $_POST['id'];
	$state = $_POST['state'];

	var_dump($id);
	var_dump($state);

	$json  	     = json_decode(file_get_contents($file),  TRUE);
	$json[$id] 	 = array("state" => $state);

	file_put_contents($file, json_encode($json));
	echo "Done";
?>