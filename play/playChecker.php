<?php
/*
Name: Damian Najera
Partner: Ezequiel Rios
Professor: Dr. Cheon
Date of Last Revision: 2/29/16
Assignment: Project 1 (PHP)
*/
	include '../info/gameInfo.php';

	//handles the case where pid is not set
	if(!isset($_GET['pid'])){
		echo json_encode(array('response' => false,'reason' => "Pid not specified"));
		exit;
	}
	
	//store all the logs (game_logs.txt) into the array $logs
	$logs = explode('|', file_get_contents('../log/game_logs.txt'));

	$index = -1;
	for($i = 0; $i < count($logs); $i++){
		$gameStatus = json_decode($logs[$i],true);
		if($gameStatus['pid'] == $_GET['pid']){
			$index = $i;
			break;
		}
	}

	//case where pid was not found
	if($index == -1){
		echo json_encode(array('response' => false, 'reason' => "Unknown pid"));
		exit;
	} 

	//case where move is not set
	if(!isset($_GET['move'])){
		echo json_encode(array('response' => false,'reason' => "Move not specified"));
		exit;
	}

	//explode the coordinates into an array and check if it is well formed
	$coordinates = explode(",", $_GET['move']);
	if(count($coordinates) != 2){
		echo json_encode(array('response' => false,'reason' => "Move not well-formed"));
		exit;
	}

	//instantiate variables $x and $y with the elements in $coordinates that we known are well-formed
	list($x,$y) = $coordinates;	

	//check if $x and $y are valid coordinates
	if($x < 0 || $x >= $size){
		echo json_encode(array('response'=>false,'reason'=>"Invalid x coordinate, $x"));
		exit;
	}

	if($y < 0 || $y >= $size){
		echo json_encode(array('response'=>false,'reason'=>"Invalid y coordinate, $y"));
		exit;
	}
?>