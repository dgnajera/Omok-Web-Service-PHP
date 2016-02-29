<?php 
	include 'Place.php'; 
	include 'playChecker.php';

	class Board {
		var $size;
		var $places;
		var $isWin;
		var $isDraw;
		var $row;
		var $computerWin;
		var $computerDraw;
		var $computerRow;
		var $strategy;
		var $playerMoves;
		var $computerMoves;

		//Board Constructor
		function Board($size){
			$this->size = $size;
			$this->places = [];
			$this->isWin = false;
			$this->isDraw = false;
			$this->row = [];

			for($i = 0; $i < $size; $i++)
				for($j = 0; $j < $size; $j++)
					array_push($this->places, new Place($i,$j));
		}

		function getSize(){
			return $this->size;
		}

		function placeStone($x,$y,$player){
			$place = $this -> at($x,$y);
			if($place->hasStone())
				exit;
		
			$response = true;
			$this->checkWin($x,$y,$player); 
			if(empty($this->$row))
				$this->isWin = true;
			$this->checkDraw();

			if(!$this->isWin && checkDraw())
				$this->draw = true;

			if($gameStatus['strategy']=="Random")
				exit;

			echo json_encode(array(
				'response'=>$response,
				'ack_move'=> json_encode(array(
					'x'=>$x,
					'y'=>$y,
					'isWin'=>$this->isWin,
					'isDraw'=>$this->isDraw,
					'row'=>$this->row
				)),
				'move'=> json_encode(array(	
					'x'=>$computerX,
					'y'=>$computerY,
					'isWin'=>$computerWin,
					'isDraw'=>$computerDraw,
					'row'=>$computerRow
				))
			));
		}

		function at($x,$y){
			foreach($this->places as &$place)
				if($place->getX() == $x-1 && $place->getY() == $y-1)
					return $place;
			return null;
		}

		function checkWin($x,$y,$player){
			//check horizontal wins 
			if($this->at($x,$y,$player) == $this->at($x-1,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x-2,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x-3,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x-4,$y,$player))
					$this->row = array($x-4,$y,$x-3,$y,$x-2,$y,$x-1,$y,$x,$y);
			

			if($this->at($x,$y,$player) == $this->at($x-1,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x-2,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x-3,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+1,$y,$player))
					$this->row =  array($x+1,$y,$x,$y,$x-1,$y,$x-2,$y,$x-3,$y);

			if($this->at($x,$y,$player) == $this->at($x-1,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x-2,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+1,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+2,$y,$player))
					$this->row =  array($x+2,$y,$x+1,$y,$x,$y,$x-1,$y,$x-2,$y);

			if($this->at($x,$y,$player) == $this->at($x-1,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+1,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+2,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+3,$y,$player))
					$this->row =  array($x+3,$y,$x+2,$y,$x+1,$y,$x,$y,$x-1,$y);

			if($this->at($x,$y,$player) == $this->at($x+1,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+2,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+3,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+4,$y,$player))
					$this->row =  array($x+4,$y,$x+3,$y,$x+2,$y,$x+1,$y,$x,$y);

			//check vertical wins
			if($this->at($x,$y,$player) == $this->at($x,$y-1,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y-2,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y-3,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y-4,$player))
					$this->row =  array($x,$y-4,$x,$y-3,$x,$y-2,$x,$y-1,$x,$y);
			

			if($this->at($x,$y,$player) == $this->at($x,$y-1,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y-2,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y-3,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+1,$player))
					$this->row =  array($x,$y+1,$x,$y,$x,$y-1,$x,$y-2,$x,$y-3);

			if($this->at($x,$y,$player) == $this->at($x,$y-1,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y-2,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+1,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+2,$player))
					$this->row =  array($x,$y+2,$x,$y+1,$x,$y,$x,$y-1,$x,$y-2);

			if($this->at($x,$y,$player) == $this->at($x,$y-1,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+1,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+2,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+3,$player))
					$this->row =  array($x,$y+3,$x,$y+2,$x,$y+1,$x,$y,$x,$y-1);

			if($this->at($x,$y,$player) == $this->at($x,$y+1,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+2,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+3,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+4,$player))
					$this->row =  array($x,$y+4,$x,$y+3,$x,$y+2,$x,$y+1,$x,$y);

			//check diagonal wins (top left to bottom right)
			if($this->at($x,$y,$player) == $this->at($x-1,$y-1,$player) &&
				$this->at($x,$y,$player) == $this->at($x-2,$y-2,$player) &&
				$this->at($x,$y,$player) == $this->at($x-3,$y-3,$player) &&
				$this->at($x,$y,$player) == $this->at($x-4,$y-4,$player))
					$this->row =  array($x-4,$y-4,$x-3,$y-3,$x-2,$y-2,$x-1,$y-1,$x,$y);
			
			if($this->at($x,$y,$player) == $this->at($x-1,$y-1,$player) &&
				$this->at($x,$y,$player) == $this->at($x-2,$y-2,$player) &&
				$this->at($x,$y,$player) == $this->at($x-3,$y-3,$player) &&
				$this->at($x,$y,$player) == $this->at($x+1,$y+1,$player))
					$this->row =  array($x+1,$y+1,$x,$y,$x-1,$y-1,$x-2,$y-2,$x-3,$y-3);

			if($this->at($x,$y,$player) == $this->at($x-1,$y-1,$player) &&
				$this->at($x,$y,$player) == $this->at($x-2,$y-2,$player) &&
				$this->at($x,$y,$player) == $this->at($x+1,$y+1,$player) &&
				$this->at($x,$y,$player) == $this->at($x+2,$y+2,$player))
					$this->row =  array($x+2,$y+2,$x+1,$y+1,$x,$y,$x-1,$y-1,$x-2,$y-2);

			if($this->at($x,$y,$player) == $this->at($x-1,$y-1,$player) &&
				$this->at($x,$y,$player) == $this->at($x+1,$y+1,$player) &&
				$this->at($x,$y,$player) == $this->at($x+2,$y+2,$player) &&
				$this->at($x,$y,$player) == $this->at($x+3,$y+3,$player))
					$this->row =  array($x+3,$y+3,$x+2,$y+2,$x+1,$y+1,$x,$y,$x-1,$y-1);

			if($this->at($x,$y,$player) == $this->at($x+1,$y+1,$player) &&
				$this->at($x,$y,$player) == $this->at($x+2,$y+2,$player) &&
				$this->at($x,$y,$player) == $this->at($x+3,$y+3,$player) &&
				$this->at($x,$y,$player) == $this->at($x+4,$y+4,$player))
					$this->row =  array($x+4,$y+4,$x+3,$y+3,$x+2,$y+2,$x+1,$y+1,$x,$y);
		}

		function checkDraw(){
			// $draw = true;
			foreach($this->places as $place)
				if(!$place->hasStone())
					// $draw = false;
					return false;
			return true;
		}

		function load($gameStatus){
			$this->strategy = $gameStatus['strategy'];
			$this->playerMoves = $gameStatus['playerMoves'];
			$this->computerMoves = $gameStatus['computerMoves'];

			if(!empty($gameStatus['playerMoves'])){
				foreach($gameStatus['playerMoves'] as $currentMove){
					list($x,$y) = $currentMove;
					$this->placeStone($x,$y);
				}
			}

			if(!empty($gameStatus['computerMoves'])){
				foreach($gameStatus['computerMoves'] as $currentMove){
					list($x,$y) = $currentMove;
					$this->placeStone($x,$y);
				}
			}
		}
	}

	class RandomStrategy {
		var $board;

		function RandomStrategy($board){
			$this->board = $board;
		}

		function placeStone(){
			$loop = true;
			while($loop){
				$randomX = rand(0,$this->board->getSize()-1);
				$randomY = rand(0,$this->board->getSize()-1);

				if(!$this->board->at($randomX,$randomY)->hasStone()){
					$this->board->placeStone($randomX,$randomY,"computer");
					$loop = false;
					$this->board->checkWin($randomX,$randomY);
					$this->board->checkDraw($randomX,$randomY);

					return json_encode(array(

					));
				}
			}
		}
	}
?>