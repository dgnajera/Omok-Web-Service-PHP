<?php 
	include 'Place.php'; 

	class Board {
		var $size;
		var $places;
		var $isWin;
		var $isDraw;
		var $row;

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
			$place = $this -> at($x,$y,$player);
			if(is_null($place))
				echo " NULL";
			else echo "NOT NULL";
		}

			// if($response){
			// 	checkWin(); //write this	
			// 	checkDraw(); //write this

			// 	if($isWin){
			// 		echo json_encode(array(
			// 			['response']=>$response,
			// 			['ack_move']=>json_encode(array(
			// 				['x']=>$x,
			// 				['y']=>$y,
			// 				['isWin']=>$isWin,
			// 				['isDraw']=>$isDraw,
			// 				['row']=>$row
			// 			))
			// 		));
			// 	}

			// 	if(!$isWin || !$isDraw){
			// 		//computer move
			// 	}
			// }
			// echo json_encode(array(
			// 	['response']=>$response,
			// 	['ack_move']=>json_encode(array(
			// 		['x']=>$x,
			// 		['y']=>$y,
			// 		['isWin']=>$isWin,
			// 		['isDraw']=>$isDraw,
			// 		['row']=>$row
			// 	)),
			// 	['move']=>json_encode(array(
			// 		['x']=>$AIx,
			// 		['y']=>$
			// 	))
			// ));
		// }
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
					return array($x-4,$y,$x-3,$y,$x-2,$y,$x-1,$y,$x,$y);
			

			if($this->at($x,$y,$player) == $this->at($x-1,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x-2,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x-3,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+1,$y,$player))
					return array($x+1,$y,$x,$y,$x-1,$y,$x-2,$y,$x-3,$y);

			if($this->at($x,$y,$player) == $this->at($x-1,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x-2,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+1,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+2,$y,$player))
					return array($x+2,$y,$x+1,$y,$x,$y,$x-1,$y,$x-2,$y);

			if($this->at($x,$y,$player) == $this->at($x-1,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+1,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+2,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+3,$y,$player))
					return array($x+3,$y,$x+2,$y,$x+1,$y,$x,$y,$x-1,$y);

			if($this->at($x,$y,$player) == $this->at($x+1,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+2,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+3,$y,$player) &&
				$this->at($x,$y,$player) == $this->at($x+4,$y,$player))
					return array($x+4,$y,$x+3,$y,$x+2,$y,$x+1,$y,$x,$y);

			//check vertical wins
			if($this->at($x,$y,$player) == $this->at($x,$y-1,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y-2,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y-3,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y-4,$player))
					return array($x,$y-4,$x,$y-3,$x,$y-2,$x,$y-1,$x,$y);
			

			if($this->at($x,$y,$player) == $this->at($x,$y-1,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y-2,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y-3,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+1,$player))
					return array($x,$y+1,$x,$y,$x,$y-1,$x,$y-2,$x,$y-3);

			if($this->at($x,$y,$player) == $this->at($x,$y-1,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y-2,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+1,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+2,$player))
					return array($x,$y+2,$x,$y+1,$x,$y,$x,$y-1,$x,$y-2);

			if($this->at($x,$y,$player) == $this->at($x,$y-1,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+1,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+2,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+3,$player))
					return array($x,$y+3,$x,$y+2,$x,$y+1,$x,$y,$x,$y-1);

			if($this->at($x,$y,$player) == $this->at($x,$y+1,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+2,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+3,$player) &&
				$this->at($x,$y,$player) == $this->at($x,$y+4,$player))
					return array($x,$y+4,$x,$y+3,$x,$y+2,$x,$y+1,$x,$y);

			return null;

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
	// $board = new Board(15);
	// $random = new RandomStrategy($board);
	// $random->placeStone();


	$board = new Board(15);
	$board->placeStone(0,0,"player");
	// echo $board->at(16,16)->hasStone();
	// echo $board->at(16,16)->getStone();

	// foreach($board->places as &$place){
	// 	echo "(";
	// 	echo $place->getX();
	// 	echo ",";
	// 	echo $place->getY();
	// 	echo ") ";
	// }
?>