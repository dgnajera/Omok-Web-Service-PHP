<?php
	include 'newGameChecker.php';

	$pid = uniqid();
	$file = fopen("../log/game_logs.txt","a");

	fwrite($file, json_encode(array(
		'pid' => $pid, 
		'strategy' => $_GET['strategy'],
		'playerMoves' => [],
		'computerMoves' => []
	)) . PHP_EOL);

	fclose($file);
	
	echo json_encode(array('response' => true, 'pid' => $pid));	
?>
