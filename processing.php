<?php
//call placeShips.php
include_once "placeShips.php";

//create seesion var grid  **bug - each call updates session variable, must be program flow fault
$_SESSION['grid'] = $encoderRing;
$decoderRing = json_decode($encoderRing); 


?>