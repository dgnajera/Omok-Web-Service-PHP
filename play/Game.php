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

		var $logs;
		var $gameStatus;
		var $index;


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

		function placeStone($x,$y){
			$place = $this -> at($x,$y);

			//check if place has stone
			if($place->hasStone())
				exit;
			
			//place the stone
			$place->placeStone("player");

			//update the list of player moves with the new move [$x,$y]
			array_push($this->gameStatus['playerMoves'],[(int)$x,(int)$y]);			

			//check for player win
			$this->checkWin($x,$y); 

			//check for player draw if there is no player win
			if(!$this->isWin && $this->checkDraw("player"))
				$this->isDraw = true;

			//Find strategy to be used and have it generate a move and
			//update the list of computer moves with the new move [$computerX,$computerY]
			if($this->strategy=="Random"){
				$randomComputer = new RandomStrategy($this);
				list($computerX,$computerY) = $randomComputer->placeStone(); 
				array_push($this->gameStatus['computerMoves'],[(int)$computerX,(int)$computerY]);			
			}
			
			//update the exploded array containing all the game logs at the appropriate
			//index and update the json string, the implode all logs back to a single string
			//that will be put into the game_logs.txt file
			$this->logs[$this->index] = json_encode($this->gameStatus);
			$updatedLogs = implode('|',$this->logs);

			file_put_contents('../log/game_logs.txt', $updatedLogs);

			//generate server response
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
			$place = null;

			foreach($this->places as $p)
				if($p->getX() == $x && $p->getY() == $y){
					$place = $p;
					break;
				}
			
			return $place;
		}

		function hasNullPlaces($places){
			foreach($places as $place)
				if(is_null($place))
					return true;
			return false;
		}

		function placesHaveSameStone($places){
			list($place1,$place2,$place3,$place4,$place5) = $places;
			if($place1->getStone() == $place2->getStone() &&
					$place1->getStone() == $place3->getStone() &&
					$place1->getStone() == $place4->getStone() &&
					$place1->getStone() == $place5->getStone()){
						return true;
					}
			return false;
		}

		function checkWin($x,$y){
			//check horizontal wins 
			$place1 = $this->at($x,$y);
			$place2 = $this->at($x-1,$y);
			$place3 = $this->at($x-2,$y);
			$place4 = $this->at($x-3,$y);
			$place5 = $this->at($x-4,$y);

			if(!$this->hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
				if($this->placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
						if($place1->getStone() == "player"){
							$this->row = json_encode(array($x-4,$y,$x-3,$y,$x-2,$y,$x-1,$y,$x,$y));
							$this->isWin = true;
						}

						if($place1->getStone() == "computer"){
							$this->computerRow = array($x-4,$y,$x-3,$y,$x-2,$y,$x-1,$y,$x,$y);
							$this->computerWin = true;
						}
				}
			}

			// $place1 = $this->at($x,$y);
			// $place2 = $this->at($x-1,$y);
			// $place3 = $this->at($x-2,$y);
			// $place4 = $this->at($x-3,$y);
			// $place5 = $this->at($x+1,$y);

			// if(!$this->hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
			// 	if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
			// 			if($place1->getStone() == "player"){
			// 				$this->row =  array($x+1,$y,$x,$y,$x-1,$y,$x-2,$y,$x-3,$y);
			// 				$this->isWin = true;
			// 			}

			// 			if($place1->getStone() == "computer"){
			// 				$this->row =  array($x+1,$y,$x,$y,$x-1,$y,$x-2,$y,$x-3,$y);
			// 				$this->computerWin = true;
			// 			}
			// 	}
			// }

			// $place1 = $this->at($x,$y);
			// $place2 = $this->at($x-1,$y);
			// $place3 = $this->at($x-2,$y);
			// $place4 = $this->at($x+1,$y);
			// $place5 = $this->at($x+2,$y);

			// if(!$this->hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
			// 	if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
			// 			if($place1->getStone() == "player"){
			// 				$this->row =  array($x+2,$y,$x+1,$y,$x,$y,$x-1,$y,$x-2,$y);
			// 				$this->isWin = true;
			// 			}

			// 			if($place1->getStone() == "computer"){
			// 				$this->row =  array($x+2,$y,$x+1,$y,$x,$y,$x-1,$y,$x-2,$y);
			// 				$this->computerWin = true;
			// 			}
			// 	}
			// }

			// $place1 = $this->at($x,$y);
			// $place2 = $this->at($x-1,$y);
			// $place3 = $this->at($x+1,$y);
			// $place4 = $this->at($x+2,$y);
			// $place5 = $this->at($x+3,$y);

			// if(!$this->hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
			// 	if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
			// 			if($place1->getStone() == "player"){
			// 				$this->row =  array($x+3,$y,$x+2,$y,$x+1,$y,$x,$y,$x-1,$y);
			// 				$this->isWin = true;
			// 			}

			// 			if($place1->getStone() == "computer"){
			// 				$this->row =  array($x+3,$y,$x+2,$y,$x+1,$y,$x,$y,$x-1,$y);
			// 				$this->computerWin = true;
			// 			}
			// 	}
			// }

			// $place1 = $this->at($x,$y);
			// $place2 = $this->at($x+1,$y);
			// $place3 = $this->at($x+2,$y);
			// $place4 = $this->at($x+3,$y);
			// $place5 = $this->at($x+4,$y);

			// if(!$this->hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
			// 	if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
			// 			if($place1->getStone() == "player"){
			// 				$this->row =  array($x+4,$y,$x+3,$y,$x+2,$y,$x+1,$y,$x,$y);
			// 				$this->isWin = true;
			// 			}

			// 			if($place1->getStone() == "computer"){
			// 				$this->row =  array($x+4,$y,$x+3,$y,$x+2,$y,$x+1,$y,$x,$y);
			// 				$this->computerWin = true;
			// 			}
			// 	}
			// }
/*
			// check vertical wins
			$place1 = $this->at($x,$y);
			$place2 = $this->at($x,$y-1);
			$place3 = $this->at($x,$y-2);
			$place4 = $this->at($x,$y-3);
			$place5 = $this->at($x,$y-4);

			if(!hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
				if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
						if($place1->getStone() == "player"){
							$this->row =  array($x,$y-4,$x,$y-3,$x,$y-2,$x,$y-1,$x,$y);
							$this->isWin = true;
						}

						if($place1->getStone() == "computer"){
							$this->row =  array($x,$y-4,$x,$y-3,$x,$y-2,$x,$y-1,$x,$y);
							$this->computerWin = true;
						}
				}
			}

			$place1 = $this->at($x,$y);
			$place2 = $this->at($x,$y-1);
			$place3 = $this->at($x,$y-2);
			$place4 = $this->at($x,$y-3);
			$place5 = $this->at($x,$y+1);

			if(!hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
				if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
						if($place1->getStone() == "player"){
							$this->row =  array($x,$y+1,$x,$y,$x,$y-1,$x,$y-2,$x,$y-3);
							$this->isWin = true;
						}

						if($place1->getStone() == "computer"){
							$this->computerRow =  array($x,$y+1,$x,$y,$x,$y-1,$x,$y-2,$x,$y-3);
							$this->computerWin = true;
						}
				}
			}

			$place1 = $this->at($x,$y);
			$place2 = $this->at($x,$y-1);
			$place3 = $this->at($x,$y-2);
			$place4 = $this->at($x,$y+1);
			$place5 = $this->at($x,$y+2);

			if(!hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
				if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
						if($place1->getStone() == "player"){
							$this->row =  array($x,$y+2,$x,$y+1,$x,$y,$x,$y-1,$x,$y-2);
							$this->isWin = true;
						}

						if($place1->getStone() == "computer"){
							$this->computerRow =  array($x,$y+2,$x,$y+1,$x,$y,$x,$y-1,$x,$y-2);
							$this->computerWin = true;
						}
				}
			}

			$place1 = $this->at($x,$y);
			$place2 = $this->at($x,$y-1);
			$place3 = $this->at($x,$y+1);
			$place4 = $this->at($x,$y+2);
			$place5 = $this->at($x,$y+3);

			if(!hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
				if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
						if($place1->getStone() == "player"){
							$this->row =  array($x,$y+3,$x,$y+2,$x,$y+1,$x,$y,$x,$y-1);
							$this->isWin = true;
						}

						if($place1->getStone() == "computer"){
							$this->computerRow =  array($x,$y+3,$x,$y+2,$x,$y+1,$x,$y,$x,$y-1);
							$this->computerWin = true;
						}
				}
			}

			$place1 = $this->at($x,$y);
			$place2 = $this->at($x,$y+1);
			$place3 = $this->at($x,$y+2);
			$place4 = $this->at($x,$y+3);
			$place5 = $this->at($x,$y+1);

			if(!hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
				if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
						if($place1->getStone() == "player"){
							$this->row =  array($x,$y+4,$x,$y+3,$x,$y+2,$x,$y+1,$x,$y);
							$this->isWin = true;
						}

						if($place1->getStone() == "computer"){
							$this->computerRow =  array($x,$y+4,$x,$y+3,$x,$y+2,$x,$y+1,$x,$y);
							$this->computerWin = true;
						}
				}
			}

			// check diagonal wins (top left to bottom right)

			$place1 = $this->at($x,$y);
			$place2 = $this->at($x-1,$y-1);
			$place3 = $this->at($x-2,$y-2);
			$place4 = $this->at($x-3,$y-3);
			$place5 = $this->at($x-4,$y-4);

			if(!hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
				if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
						if($place1->getStone() == "player"){
							$this->row =  array($x-4,$y-4,$x-3,$y-3,$x-2,$y-2,$x-1,$y-1,$x,$y);
							$this->isWin = true;
						}

						if($place1->getStone() == "computer"){
							$this->computerRow =  array($x-4,$y-4,$x-3,$y-3,$x-2,$y-2,$x-1,$y-1,$x,$y);
							$this->computerWin = true;
						}
				}
			}

			$place1 = $this->at($x,$y);
			$place2 = $this->at($x-1,$y-1);
			$place3 = $this->at($x-2,$y-2);
			$place4 = $this->at($x-3,$y-3);
			$place5 = $this->at($x-4,$y-4);

			if(!hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
				if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
						if($place1->getStone() == "player"){
							$this->row =  array($x+1,$y+1,$x,$y,$x-1,$y-1,$x-2,$y-2,$x-3,$y-3);
							$this->isWin = true;
						}

						if($place1->getStone() == "computer"){
							$this->computerRow =  array($x+1,$y+1,$x,$y,$x-1,$y-1,$x-2,$y-2,$x-3,$y-3);
							$this->computerWin = true;
						}
				}
			}

			$place1 = $this->at($x,$y);
			$place2 = $this->at($x-1,$y-1);
			$place3 = $this->at($x-2,$y-2);
			$place4 = $this->at($x-3,$y-3);
			$place5 = $this->at($x-4,$y-4);

			if(!hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
				if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
						if($place1->getStone() == "player"){
							$this->row =  array($x+2,$y+2,$x+1,$y+1,$x,$y,$x-1,$y-1,$x-2,$y-2);
							$this->isWin = true;
						}

						if($place1->getStone() == "computer"){
							$this->computerRow =  array($x+2,$y+2,$x+1,$y+1,$x,$y,$x-1,$y-1,$x-2,$y-2);
							$this->computerWin = true;
						}
				}
			}

			$place1 = $this->at($x,$y);
			$place2 = $this->at($x-1,$y-1);
			$place3 = $this->at($x-2,$y-2);
			$place4 = $this->at($x-3,$y-3);
			$place5 = $this->at($x-4,$y-4);

			if(!hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
				if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
						if($place1->getStone() == "player"){
							$this->row =  array($x+3,$y+3,$x+2,$y+2,$x+1,$y+1,$x,$y,$x-1,$y-1);
							$this->isWin = true;
						}

						if($place1->getStone() == "computer"){
							$this->computerRow =  array($x+3,$y+3,$x+2,$y+2,$x+1,$y+1,$x,$y,$x-1,$y-1);
							$this->computerWin = true;
						}
				}
			}

			$place1 = $this->at($x,$y);
			$place2 = $this->at($x-1,$y-1);
			$place3 = $this->at($x-2,$y-2);
			$place4 = $this->at($x-3,$y-3);
			$place5 = $this->at($x-4,$y-4);

			if(!hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
				if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
						if($place1->getStone() == "player"){
							$this->row =  array($x+4,$y+4,$x+3,$y+3,$x+2,$y+2,$x+1,$y+1,$x,$y);
							$this->isWin = true;
						}

						if($place1->getStone() == "computer"){
							$this->computerRow =  array($x+4,$y+4,$x+3,$y+3,$x+2,$y+2,$x+1,$y+1,$x,$y);
							$this->computerWin = true;
						}
				}
			}

			$place1 = $this->at($x,$y);
			$place2 = $this->at($x-1,$y-1);
			$place3 = $this->at($x-2,$y-2);
			$place4 = $this->at($x-3,$y-3);
			$place5 = $this->at($x-4,$y-4);
*/
			//check diagonal wins (bottom left to top right)
			// if(!hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
			// 	if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
			// 			if($place1->getStone() == "player"){
			// 				$this->row =  array($x-4,$y-4,$x-3,$y-3,$x-2,$y-2,$x-1,$y-1,$x,$y);
			// 				$this->isWin = true;
			// 			}

			// 			if($place1->getStone() == "computer"){
			// 				$this->computerRow =  array($x-4,$y-4,$x-3,$y-3,$x-2,$y-2,$x-1,$y-1,$x,$y);
			// 				$this->computerWin = true;
			// 			}
			// 	}
			// }
	
			// $place1 = $this->at($x,$y);
			// $place2 = $this->at($x-1,$y-1);
			// $place3 = $this->at($x-2,$y-2);
			// $place4 = $this->at($x-3,$y-3);
			// $place5 = $this->at($x-4,$y-4);

			// if(!hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
			// 	if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
			// 			if($place1->getStone() == "player"){
			// 				$this->row =  array($x+1,$y+1,$x,$y,$x-1,$y-1,$x-2,$y-2,$x-3,$y-3);
			// 				$this->isWin = true;
			// 			}

			// 			if($place1->getStone() == "computer"){
			// 				$this->computerRow =  array($x+1,$y+1,$x,$y,$x-1,$y-1,$x-2,$y-2,$x-3,$y-3);
			// 				$this->computerWin = true;
			// 			}
			// 	}
			// }

			// $place1 = $this->at($x,$y);
			// $place2 = $this->at($x-1,$y-1);
			// $place3 = $this->at($x-2,$y-2);
			// $place4 = $this->at($x-3,$y-3);
			// $place5 = $this->at($x-4,$y-4);

			// if(!hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
			// 	if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
			// 			if($place1->getStone() == "player"){
			// 				$this->row =  array($x+2,$y+2,$x+1,$y+1,$x,$y,$x-1,$y-1,$x-2,$y-2);
			// 				$this->isWin = true;
			// 			}

			// 			if($place1->getStone() == "computer"){
			// 				$this->computerRow =  array($x+2,$y+2,$x+1,$y+1,$x,$y,$x-1,$y-1,$x-2,$y-2);
			// 				$this->computerWin = true;
			// 			}
			// 	}
			// }

			// $place1 = $this->at($x,$y);
			// $place2 = $this->at($x-1,$y-1);
			// $place3 = $this->at($x-2,$y-2);
			// $place4 = $this->at($x-3,$y-3);
			// $place5 = $this->at($x-4,$y-4);

			// if(!hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
			// 	if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
			// 			if($place1->getStone() == "player"){
			// 				$this->row =  array($x+3,$y+3,$x+2,$y+2,$x+1,$y+1,$x,$y,$x-1,$y-1);
			// 				$this->isWin = true;
			// 			}

			// 			if($place1->getStone() == "computer"){
			// 				$this->computerRow =  array($x+3,$y+3,$x+2,$y+2,$x+1,$y+1,$x,$y,$x-1,$y-1);
			// 				$this->computerWin = true;
			// 			}
			// 	}
			// }

			// $place1 = $this->at($x,$y);
			// $place2 = $this->at($x-1,$y-1);
			// $place3 = $this->at($x-2,$y-2);
			// $place4 = $this->at($x-3,$y-3);
			// $place5 = $this->at($x-4,$y-4);

			// if(!hasNullPlaces(array($place1,$place2,$place3,$place4,$place5))){
			// 	if(placesHaveSameStone(array($place1,$place2,$place3,$place4,$place5))){
			// 			if($place1->getStone() == "player"){
			// 				$this->row =  array($x+4,$y+4,$x+3,$y+3,$x+2,$y+2,$x+1,$y+1,$x,$y);
			// 				$this->isWin = true;
			// 			}

			// 			if($place1->getStone() == "computer"){
			// 				$this->computerRow =  array($x+4,$y+4,$x+3,$y+3,$x+2,$y+2,$x+1,$y+1,$x,$y);
			// 				$this->computerWin = true;
			// 			}
			// 	}
			// }
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

		function load($logs,$index){
			$this->logs = $logs;
			$this->index = $index;
			$this->gameStatus = json_decode($logs[$index],true);
			$this->strategy = $this->gameStatus['strategy'];
			$this->playerMoves = $this->gameStatus['playerMoves'];
			$this->computerMoves = $this->gameStatus['computerMoves'];

			if(!empty($this->gameStatus['playerMoves'])){
				foreach($this->gameStatus['playerMoves'] as $currentMove){
					list($x,$y) = $currentMove;
					$this->at($x,$y)->placeStone("player");
				}
			}

			if(!empty($this->gameStatus['computerMoves'])){
				foreach($this->gameStatus['computerMoves'] as $currentMove){
					list($x,$y) = $currentMove;
					$this->at($x,$y)->placeStone("computer");
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
					// $this->board->computerWin = $this->board->checkWin($randomX,$randomY);
					$this->board->computerDraw = $this->board->checkDraw($randomX,$randomY,"computer");
					return array($randomX,$randomY);
				}
			}
		}
	}
?>