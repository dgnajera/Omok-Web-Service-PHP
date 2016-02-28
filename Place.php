<?php 	
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

		function placeStone($identifier){
			$this->stone = $identifier;
		 	$this->hasStone = true;
		}
	}
?>