<?php
/*
Name: Damian Najera
Partner: Ezequiel Rios
Professor: Dr. Cheon
Date of Last Revision: 2/29/16
Assignment: Project 1 (PHP)
*/
	include '../info/gameInfo.php';

	//handles the case where strategy is not set
	if(!isset($_GET['strategy'])){
		echo json_encode(array('response' => false,'reason' => "Strategy not specified"));
		exit;
	}

	//handles the case where strategy is set but not an available strategy on the server
	if((isset($_GET['strategy'])) && !in_array($_GET['strategy'], $strategies)){
			echo json_encode(array('response' => false, 'reason' => "Unknown strategy"));
			exit;
	}
?>