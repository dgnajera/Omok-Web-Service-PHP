<?php
	include '../info/gameInfo.php';
	include 'playChecker.php';
	include 'Game.php';

	$gameStatus = json_decode($logs[$index],true);

	$board = new Board($size);
	$board->load($gameStatus);
	$board->placeStone($x,$y);
	// $board->placeStone($x,$y,"player");
	
	// foreach($board->places as &$place){
	// 	echo "(";
	// 	echo $place->getX();
	// 	echo ",";
	// 	echo $place->getY();
	// 	echo ") ";
	// }

?>
