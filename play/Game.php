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

		var $playerMoves;
		var $computerMoves;
		var $strategy;


		//Board Constructor
		function Board($size){
			$this->size = $size;
			$this->places = [];
			$this->isWin = false;
			$this->isDraw = false;
			$this->row = [];
			$this->computerWin = false;
			$this->computerDraw = false;
			$this->computerRow = [];

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
		
			$this->checkWin($x,$y,$player); 
			if(!empty($this->$row))
				$this->isWin = true;

			if(!$this->isWin && $this->checkDraw("player"))
				$this->isDraw = true;

			if($this->strategy=="Random"){
				$randomComputer = new RandomStrategy($this);
				list($computerX,$computerY) = $randomComputer->placeStone(); 
			}
			
			$serverResponse['response'] = true;
			$serverResponse['ack_move'] = $this->generateMoveResponse($x,$y,"player");
			$serverResponse['move'] = $this->generateMoveResponse($computerX,$computerY,"computer");

			echo json_encode($serverResponse);
		}

		function generateMoveResponse($x,$y,$identifier){
			if($identifier == 'player'){
				if($this->isWin){
					return json_decode(
						'{"x":' .$x.
						',"y":' .$y.
						',"isWin":true' . 
						',"isDraw":false' . 
						',"row":'.$this->rowContents($this->row). 
						'}'
					);
				}

				else if($this->isDraw){
					return json_decode(
						'{"x":' .$x.
						',"y":' .$y.
						',"isWin":false' . 
						',"isDraw":true' . 
						',"row":'.$this->rowContents($this->row). 
						'}'
					);
				}

				else 
					return json_decode(
						'{"x":' .$x.
						',"y":' .$y.
						',"isWin":false' . 
						',"isDraw":false' . 
						',"row":'.$this->rowContents($this->row). 
						'}'
					);
			}

			//$identifier != player, so it must be $identifier == computer
			if($this->computerWin){
				return json_decode(
					'{"x":' .$x.
					',"y":' .$y.
					',"isWin":true' . 
					',"isDraw":false' . 
					',"row":'.$this->rowContents($this->computerRow). 
					'}'
				);
			}

			else if($this->computerDraw){
				return json_decode(
					'{"x":' .$x.
					',"y":' .$y.
					',"isWin":false' . 
					',"isDraw":true' . 
					',"row":'.$this->rowContents($this->computerRow). 
					'}'
				);
			}

			else 
				return json_decode(
					'{"x":' .$x.
					',"y":' .$y.
					',"isWin":false' . 
					',"isDraw":false' . 
					',"row":'.$this->rowContents($this->computerRow). 
					'}'
				);
		}

		function rowContents($row){
			$rowContents = '[';
			foreach($row as $currentCoordinate)
				$rowContents .= $currentCoordinate;
			$rowContents .= ']';
			return $rowContents;
		}

		function at($x,$y){
			foreach($this->places as &$place)
				if($place->getX() == $x && $place->getY() == $y)
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

		function checkDraw($identifier){
			foreach($this->places as $place)
				if(!$place->hasStone())
					return false;

			if($identifier == "player")
				$this->isDraw = true;

			if($identifier == "computer")
				$this->computerDraw = true;

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
			while(true){
				$randomX = rand(0,$this->board->getSize()-1);
				$randomY = rand(0,$this->board->getSize()-1);
				if(!$this->board->at($randomX,$randomY)->hasStone()){
					$this->board->at($randomX,$randomY)->placeStone("computer");
					$this->board->computerWin = $this->board->checkWin($randomX,$randomY,"computer");
					$this->board->computerDraw = $this->board->checkDraw($randomX,$randomY,"computer");
					return array($randomX,$randomY);
				}
			}
		}
	}
?>


