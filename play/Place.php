<?php 	
/*
Name: Damian Najera
Partner: Ezequiel Rios
Professor: Dr. Cheon
Date of Last Revision: 2/29/16
Assignment: Project 1 (PHP)
*/
	class Place {
		private $x;
		private $y;
		private $stone;
		private $hasStone;

		//Place Constructor
		function Place($x,$y){
		 	$this->x = $x;
		 	$this->y = $y;
			$this->hasStone = false;
		}

		//Getters
		function getX(){
		 	return $this->x;
		}

		function getY(){
		 	return $this->y;
		}

		function getStone(){
			return $this->stone;
		}
		
		function hasStone(){
			return $this->hasStone;
		}

		//places a specific stone on the place
		function placeStone($identifier){
			$this->stone = $identifier;
		 	$this->hasStone = true;
		}
	}
?>