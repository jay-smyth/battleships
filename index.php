<?php
session_start();
 include "processing.php";
 include "shots.php";
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="battleStyles.css"/> 
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

</head>
<body>
<div class="container">

	<div class="row justify-content-start">
	
		<div class="col-2"></div>

		<div class="col-8">
			<h1 class="text-center">Battleships</h1>
		</div>

		<div class="col-2"></div>
		
	</div>
	
	
	
	<div class="row">

		<div class="col-2"></div>

		<div class="col-8">
			<table class="mx-auto w-auto">
			
			<?php 
				for($i = 0; $i < 9; $i++){
			?><tr>
					<?php
						for($y = 0; $y <9; $y++){
							
					?><td class="table-cell tbl-alt" id="<?php echo strval($i) . strval($y);?>"><span style="visibility:hidden; display:none;"><?php echo $decoderRing[$i][$y]; ?></span></td>
						<?php } ?>

				</tr><?php } ?>
				
			</table>
			
			<p id="shot-Count" class="text-center">You have a total of 5 shots left</p>
			
			
			<div class="col text-center">
				<h3 id="clear-shot-win">You have 0 shots left, fire now or clear targets?</h3>
				<br>

				<button id="submit" type="submit" style="margin:25px;" class="btn btn-danger">Fire Shots!</button>
				<button id="clear-shots" style="margin:25px;" class="btn btn-info">Clear Shots!</button>

			</div>


		</div>

		<div class="col-2">
			<div id="carrier"  style="visibility:hidden;"><img class="ship-img" src="img/carrier.jpg"/><p>Carrier sunk!</p></div>
			<div id="battleship" style="visibility:hidden;"><img class="ship-img" src="img/battleship.jpg"/><p>Battleship sunk!</p></div>
			<div id="cruiser" style="visibility:hidden;"><img class="ship-img" src="img/cruiser.jpg"/><p>Cruiser sunk!</p></div>
			<div id="submarine" style="visibility:hidden;"><img class="ship-img" src="img/submarine.jpg"/><p>Submarine sunk!</p></div>
			<div id="destroyer" style="visibility:hidden;"><img class="ship-img" src="img/destroyer.jpg"/><p>Destroyer sunk!</p></div>
		</div>
		
	</div>
	
</div>

</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script>
//initialize globals
var shots = [];
var i = 0;
var shCount = 5;
var myGrid;
var id;
var hitShips = [];
var shCoor = [];
var m = 0;


//table cell click function tests for valid shots
$(".table-cell").click(function(){
	
	//get <td> id
	id = $(this).attr("id");
	
	//validate cell hasn't been fired on
	if(checkShot(id) == true){
		if(i < 5){
			$(this).css("background-color", "rgb(255,0,0)");
			shotFired(id);
			attack(id);
			id = "";
			return i++;
		} else {
			shotFired(id);
		}
	} else {
		alert("This target has been shot already");
		id = "";//clear var id
	}
});

//
function checkShot(id){
	//show var values before manipulation
	console.log(typeof(shots) + "  < Checkshot func vars > " + typeof(id));
	
	//change string id into integer
	var t = parseInt(id);
	
	//test for double click on cells that have already received a shot
	if(shots === "undefined" || shots.length == 0){
		return true;
	} else {
		//if t=id matches shCoor in array return false - bug**first call for test returns true, then second test returns true?? 
		if(!shCoor.includes(t)){
			if(!shots.includes(t)){
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

//
function shotFired(id){
	if(i < 5){
		shCount--;
		//display shot count
		animateShotCount(shCount);
		//global var id to integer
		var t = parseInt(id);
		
		console.log("Var t type = " + typeof t + "/n");
		//push id int to array shots
		shots[i] = t;
	} else {
		//display error message of no shots left
		$("#clear-shot-win").toggle(600, function(){});
		console.log(shots);
	}
}

//first call of jSON object of grid and ship positions
function attack(data){
	$.ajax({
		type: "post",
		dataType: "json",
		data: "data=" + data,
		cache: false,
		url: "coordArray.json",
		success: function(jsonObj)
		{
			myGrid = jsonObj;
			var shotsFired = shots;
			console.log(jsonObj);
			return myGrid;
		},
		error: function(e){
		console.log(e); 	
		}
	});
};

//prevent submit button click and call processAttack function
$('#submit').click('submit', function (e) {
	e.preventDefault();
	processAttack(shots, myGrid);
});


//returns all clicked cells to original colour state and resets globals
$('#clear-shots').click(function() {
	
	//return chosen cells to original colour state
	$(".table-cell").each(function(i) {
		if (this.style.backgroundColor == "rgb(255,0,0)") {
		  this.style.backgroundColor = "rgb(50,205,50)";
		} else {
		  this.style.backgroundColor = "rgb(50,205,50)";
		}
	});
	
	//reset variables
	i = 0;
	shots = [];
	shCount = 5;
	animateShotCount(shCount);
	
	//remove error message of no more shots if visible
	if($("#clear-shot-win").is(':visible')){	
		$("#clear-shot-win").toggle(600, function(){});
	} 
	//confirm arrays shots is empty
	console.log("Array - shots cleared = " + shots);
});


//ajax query from shots.php, returns global arrays for updated grid, shot Coordinates within the 9x9 grid and ship names at each hit position
function processAttack(s, g){
		$.ajax({
			type: 'post',
            url: 'shots.php',
			dataType: "json", /* This caused errors as I think the JSON object can't read properly with print_r functions in php*/
            data: {shotArray : JSON.stringify(s), thisGrid : g},
			cache: false,
            success: function (data) {
				
				//JSON data test for returned data, an error occurs but seemingly has no effect on program!! Investigate later
				try{
					console.log(JSON.parse(data));
				}
				catch(err){
					console.log("JSON format error" + err);
				}
				
				//update grid array and display to console
				myGrid = data.result1;
				console.log(data.result1);
				//Display hit ships in that round and push to array
				console.log("Returned Ship names that have been hit" + data.result2);
				hitShips.push(data.result2);
				console.log(hitShips);
				//Return coordinates of shots and pass result to function hitMiss
				console.log("Returned shot coordinates" + data.result3);
				hitMiss(data.result3);

				//reset variables
				i = 0;
				shots = [];
				shCount = 5;
				animateShotCount(shCount);
				
				//function call to display ships if sunk
				shipDown();
				
				//remove error message of no more shots if visible
				if($("#clear-shot-win").is(':visible')){	
					$("#clear-shot-win").toggle(600, function(){});
				} 
            },
			error: function(e){
				console.log(e); 	
			}
	});
}

//function that displays hit or miss grid positions, also updates global array shCoor
function hitMiss(shotPos){
	
	console.log("Passed shot coordinates" + shotPos + " ");
	
	//loop through array of hit ship names and push to global array shCoor, bug exists on shCoor.includes(t) @ line pos - 
	for(var f = 0; f < shotPos.length; f++){
		shCoor.push(shotPos[f]);
		console.log(shCoor);
	}
	
	for(var k = 0; k < myGrid.length; k++){
		for(var j = 0; j < myGrid[k].length; j++){
			
			//get grid cell strings and keys for manipulation
			var data = myGrid[k][j];
			var cell = "" + k + "" + j + "";
			var pos = document.getElementById(cell);
			
			//test for hit or miss, console log key for testing, then style on success
			if (data === "Miss!"){
				console.log(cell + " = key from grid");
				pos.classList.remove("table-cell");
				pos.style.backgroundImage = "url('./img/water-miss-sml.png')";
				pos.style.backgroundRepeat = "no-repeat";
				pos.style.backgroundPosition = "center center";
				pos.style.backgroundColor = "white";
			} else if (data === "Hit!"){
				pos.style.backgroundColor = "white";
				pos.style.backgroundRepeat = "no-repeat";
				pos.style.backgroundPosition = "center center";
				pos.style.backgroundImage = "url('./img/fire-hit-sml.png')";
				pos.classList.remove("table-cell");
				console.log(cell + " = key from grid");
			}
				
		}
	}
	
}


//animate the shot counter below the displayed grid 
function animateShotCount(c){
	$("#shot-Count").text("You have a total of " + c + " shots left").animate({
		color: 'red',
		fontSize: '1.1rem',
		opacity: '0.4'},
		"fast"
	);
	$("#shot-Count").animate({
		color: '#212529',
		fontSize: '1rem',
		opacity: '1'},
		"fast"
	);
}

//loop through hitShips and test for complete ship count to display sunk ships and finish game
function shipDown(){
	var ca = "Carrier", ba = "Battleship", cr = "Cruiser", su = "Submarine", de = "Destroyer";
	var caC = 0, baC = 0, crC = 0, suC = 0, deC = 0;
	
	for(var a = 0; a < hitShips.length; a++){
		for(var b = 0; b < hitShips[a].length; b++){
			//carrier down
			if(hitShips[a][b] == ca){
				caC++;
			}
			if(caC == 5){
				document.getElementById("carrier").style.visibility = "visible";
			}
			//battleship down
			if(hitShips[a][b] == ba){
				baC++;
			}
			if(baC == 4){
				document.getElementById("battleship").style.visibility = "visible";
			}
			//cruiser down
			if(hitShips[a][b] == cr){
				crC++;
			}
			if(crC == 3){
				document.getElementById("cruiser").style.visibility = "visible";
			}
			//submarine down
			if(hitShips[a][b] == su){
				suC++;
			}
			if(suC == 3){
				document.getElementById("submarine").style.visibility = "visible";
			}
			//destroyer down
			if(hitShips[a][b] == de){
				deC++;
			}
			if(deC == 2){
				document.getElementById("destroyer").style.visibility = "visible";
			}
		}
	}
	//Test for all ships sunk
	if(caC ==5 && baC == 4 && crC == 3 && suC == 3 && deC == 2)
		gameEnd();
}

//Pass alert prompt to reload page
function gameEnd(){
	if (confirm("Would you like to play again!?")) {
    location.reload();
  } else {
    alert("Thanks for playing");
	i = 5;
  }
}


</script>
</html>