<?php 
	$filename = "./changelog/changelog.json";
	$json_str = file_get_contents($filename);
	$json_obj = json_decode($json_str); 
	
	header('Content-Type:application/json');
	header('Access-Control-Allow-Origin: *');
	echo json_encode($json_obj,JSON_UNESCAPED_UNICODE);
?>