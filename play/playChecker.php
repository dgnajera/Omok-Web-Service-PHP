<?php
	include '../info/gameInfo.php';

	if(!isset($_GET['pid'])){
		echo json_encode(array('response' => false,'reason' => "Pid not specified"));
		exit;
	}
	
	//store all the logs (game_logs.txt) into the array $logs
	$logs = explode(PHP_EOL, file_get_contents('../log/game_logs.txt'));

	$index = -1;
	for($i = 0; $i < count($logs)-1; $i++){
		$gameStatus = json_decode($logs[$i],true);
		if($gameStatus['pid'] == $_GET['pid']){
			$index = $i;
			break;
		}
	}

	//case where pid was not found or there are no game logs
	if($index == -1 || filesize('../log/game_logs.txt') == 0){
		echo json_encode(array('response' => false, 'reason' => "Unknown pid"));
		exit;
	} 

	if(!isset($_GET['move'])){
		echo json_encode(array('response' => false,'reason' => "Move not specified"));
		exit;
	}

	$coordinates = explode(",", $_GET['move']);
	if(count($coordinates) != 2){
		echo json_encode(array('response' => false,'reason' => "Move not well-formed"));
		exit;
	}

	list($x,$y) = $coordinates;	

	if($x < 0 || $x >= $size){
		echo json_encode(array('response'=>false,'reason'=>"Invalid x coordinate, $x"));
		exit;
	}

	if($y < 0 || $y >= $size){
		echo json_encode(array('response'=>false,'reason'=>"Invalid y coordinate, $y"));
		exit;
	}
?>