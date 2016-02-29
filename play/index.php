<?php
	include '../info/gameInfo.php';
	include 'playChecker.php';
	include 'Game.php';

	$board = new Board($size);
	$board->load($logs,$index);
	$board->placeStone($x,$y);
?>