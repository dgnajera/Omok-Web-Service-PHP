<?php
	include '../info/gameInfo.php';
	include 'playChecker.php';
	include 'Game.php';


	$board = new Board($size);
	$board->load($logs,$index);
	$board->placeStone($x,$y);
	
	// foreach($board->places as $place){
	// 	echo "(";
	// 	echo $place->getX();
	// 	echo ",";
	// 	echo $place->getY();
	// 	echo ") ";
	// }

?>
