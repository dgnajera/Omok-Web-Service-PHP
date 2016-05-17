<?php
/*
Name: Damian Najera
Professor: Dr. Cheon
Date of Last Revision: 2/29/16
Assignment: Project 1 (PHP)
*/
	include '../info/gameInfo.php';
	include 'playChecker.php';
	include 'Game.php';

	//initializes the board, finds the game in games_log.txt, restores the game
	//and then places the stone in the valid ($x,$y) coordinate
	$board = new Board($size);
	$board->load($logs,$index);
	$board->placeStone($x,$y);
?>