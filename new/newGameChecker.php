<?php
	include '../info/gameInfo.php';

	if(!isset($_GET['strategy'])){
		echo json_encode(array('response' => false,'reason' => "Strategy not specified"));
		exit;
	}

	if((isset($_GET['strategy'])) && 
		($_GET['strategy'] != $strategies[0] && $_GET['strategy'] != $strategies[1])){
			echo json_encode(array('response' => false, 'reason' => "Unknown strategy"));
			exit;
	}
?>