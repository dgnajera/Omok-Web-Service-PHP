<?php
	include 'newGameChecker.php';

	$pid = uniqid();
	$file = fopen("../log/game_logs.txt","a");
	if(filesize('../log/game_logs.txt') == 0)
		fwrite($file, json_encode(array(
			'pid' => $pid, 
			'strategy' => $_GET['strategy'],
			'playerMoves' => [],
			'computerMoves' => []
		)));

	else 
		fwrite($file, '|'.json_encode(array(
			'pid' => $pid, 
			'strategy' => $_GET['strategy'],
			'playerMoves' => [],
			'computerMoves' => []
		)));

	fclose($file);
	
	echo json_encode(array('response' => true, 'pid' => $pid));	
?>
