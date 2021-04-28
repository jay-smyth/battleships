<?php
//Passed shots array and grid from ajax, test shorthand stops error when empty onload, error @ $shotArray = $_POST["shotArray"];
$shotArray = $_POST["shotArray"] ?? '';
$myGrid = $_POST["thisGrid"] ?? '';

$data = JSON_decode($shotArray, true);
//Array for hit ships name
$hitShips = []; 
//print_r($data);
//print_r($myGrid);

if(!empty($data)){
	
	foreach($data as $value){
		//JSON_Stringifiying array causes leading 0's, e.g 00, to drop e.g 01 = 1
		if(strlen($value) == 1){
			$value = "0" . $value; //reassign leading 0
		}
		//Split string to create grid keys for coordinates
		$arrKeys = str_split($value);
		//If grid keys are within range continue
		if(array_keys($myGrid, $arrKeys[0] . $arrKeys[1] == true)){
			//Test grid space for empty value else ship hit!
			if(empty($myGrid[$arrKeys[0]][$arrKeys[1]]) || $myGrid[$arrKeys[0]][$arrKeys[1]] === "Miss!"){
				//Update grid
				$myGrid[$arrKeys[0]][$arrKeys[1]] = "Miss!";
			} else {
				//Push ship hit name to array for return as JSON
				array_push($hitShips, $myGrid[$arrKeys[0]][$arrKeys[1]]);
				//Update grid
				$myGrid[$arrKeys[0]][$arrKeys[1]] = "Hit!";
			}
		}
		
	}
	//Return values as JSON Objects
	echo JSON_encode(array('result1'=>$myGrid, 'result2'=>$hitShips, 'result3'=>$data));
	//Update grid at server with new values
	$encoderRing = JSON_encode($myGrid, true);
	$updateG = fopen('coordArray.json', 'w+');
	fwrite($updateG, $encoderRing);
	fclose($updateG);
}


?>