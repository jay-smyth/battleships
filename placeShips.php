<?php
//initialize vars
$ships = array ("Carrier" => 5, "Battleship" => 4, "Cruiser" => 3, "Submarine" => 3, "Destroyer" => 2);
const board = 9;
$grid = [];
$grid = array_fill(0, board, array_fill(0,board,""));

//create random int
function rand_pos(){
	return rand(0,board-1);
}

//horizontal grid position, problems with ship overlap so iterate through potential position to test first, if possible then continue
function horiz($shipLength, $ship, &$grid){
	for($y = 0; $y < 1; $y++){
		$x=0;
		$col = rand_pos();
		$row = rand_pos();
		if($grid[$row][$col] === "" &&  $col+$shipLength < 9){
			
			for($i = 0; $i < $shipLength; $i++){
				$grid[$row][$col++];
				//if grid space isn't empty increment x
				if($grid[$row][$col] !== ""){
					$x++;
				}
			} 
			//if grid space is valid
			if($x == 0){
				//subtract ship length from col to original pos and place ship name per grid cell
				$col -= $shipLength;
				
				for($i = 0; $i < $shipLength; $i++){
					$grid[$row][$col++] = $ship;
				} 
				
			} else {
				$y--;
			}
		} else {
			$y--;
			// test for failed position placement
			//echo "Horizontal fail";
		}
	}	
}

//vertical grid position, problems with ship overlap so iterate through potential position to test first, if possible then continue
function vert($shipLength, $ship, &$grid){
	for($y = 0; $y < 1; $y++){
		$x=0;
		$col = rand_pos();
		$row = rand_pos();
		if($grid[$row][$col] === "" &&  $row+$shipLength < 9){
			
			for($i = 0; $i < $shipLength; $i++){
				$grid[$row++][$col];
				//if grid space isn't empty increment x
				if($grid[$row][$col] !== ""){
					$x++;
				}
			} 
			//if grid space is valid
			if($x == 0){
				//subtract ship length from row to original pos and place ship name per grid cell
				$row -= $shipLength;
				for($i = 0; $i < $shipLength; $i++){
					$grid[$row++][$col] = $ship;
				} 
				
			} else {$y--;}
		} else {
			$y--;
			// test for failed position placement
			//echo "Vertical fail";
		}
	}
}


//failed to complete
function diag(){
	
}

//iterate through ships and choose randomly either horizontal or vertical placement
foreach($ships as $ship => $value){
	
	$shipLength = $value;
	$randomize = rand(0,1);
	
	if($randomize == 0){
	horiz($shipLength, $ship, $grid);
	} else {
	vert($shipLength, $ship, $grid);
	}
	if($value === "Destroyer"){
		$_SESSION['active'] = 1;
	}
}

//encode grid and create file on server called coordArray.json, write to file and close
$encoderRing = json_encode($grid); 
$writeTo = fopen("coordArray.json", "w+");
fwrite($writeTo, $encoderRing);
fclose($writeTo);
?>
