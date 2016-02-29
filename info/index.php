<?php
/*
Name: Damian Najera
Partner: Ezequiel Rios
Professor: Dr. Cheon
Date of Last Revision: 2/29/16
Assignment: Project 1 (PHP)
*/
	include 'gameInfo.php';
	//displays json encoding of the information of the board
	echo json_encode(array('size' => $size,'strategies' => $strategies));
?>
