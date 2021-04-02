<?php

/*

*	File: /shapleyvalue/calculator
*	By: Nuño Sempere
* 	Date: 23 Sept 2019
* 	 
*	This file calculates the Shapley value of every member of a given coalition.

*/


## HTML Flavor 

echo '<html>';

echo '<head>
	<title>Shapley Value Calculator</title>
	<meta name="copyright" content="Nuño Sempere López-Hidalgo">
	<meta name="keywords" content="Shapley Value, Shapley value, calculate Shapley value, calculate Shapley value online free, Shapley value program">
	<meta name="description" content="Shapley value calculator">
	<link rel="stylesheet" href="CSS/main.css" type="text/css">'; 
	// This is the reference to our CSS style sheet.'
echo '</head>';

echo '<body>';

echo '<h1> Shapley Value Calculator </h1>';


## Pointer to this file

$thisFile = "index.php";

## Decide the Number of Players
 
$numPlayers = $_POST["numplayers"] ?? 3; 
	// It sees what number of players the user has selected.
	// If it's the first time the user comes to the webpage, the default will be 3


echo "<form action=".$thisFile." method='post'>
	<select name='numplayers'>";
	
	for($n = 1; $n<8; $n++){
		
		if($n == $numPlayers){
			$hasBeenSelected = "selected = 'selected'";
		}else{
			$hasBeenSelected = "";
		}
		echo "<option value =".$n." ".$hasBeenSelected.">Number of players: ".$n."</option>";
	}

	echo "</select>";
	echo "&nbsp;";
	echo "<input type='submit' value='Change' class = 'Buttons'>";
	echo "</form>";

## Power set of all players

for($n = 1; $n <= $numPlayers; $n++){
	$setOfPlayers[$n] = $n;
}

$powerSet = powerSet($setOfPlayers);

$i = 0;
foreach($powerSet as $set){
	
	$powerSetAsStrings[$i] = setToString($set);
	$i++;
}


## Forms for the value of the coalition

$getCoalitionNum = $_POST['CoalitionNum']?? 0;

if($getCoalitionNum == 0){
	//That is, if we haven't yet posted anything to ourselves regarding the size of the coalition
	echo '<form action='.$thisFile.' method = "post">';
	echo '<div class="form">';
	

	echo '<input type="hidden" name="numplayers" value='.$numPlayers.' />';
	$i = 0;
	foreach($powerSetAsStrings as $setAsString){

	echo '<p>';
		echo '<label>Value of coalition '.$setAsString.': </label>';	
		echo '<input type="number" name="CoalitionNum['.$i.']" value = "0" class = "Box">';
	echo '</p>';
	$i++;
	}
		echo '</br>';

	echo '</div>';
		echo '<input type="submit" value="Compute" class="Buttons" >';
	echo '</form>';

}else{
	
	echo '<form action='.$thisFile.' method = "post">';

	echo '<input type="hidden" name="numplayers" value='.$numPlayers.' />';
	
	echo '<div class="form">';
	$i = 0;
	foreach($powerSetAsStrings as $setAsString){

	echo '<p>';
		echo '<label>Value of coalition '.$setAsString.': </label>';	
		echo '<input type="number" name="CoalitionNum['.$i.']" value ='.$getCoalitionNum[$i].' class = "Box">';
	echo '</p>';
	$i++;
	}
		echo '</br>';

	echo '</div>';
		echo '<input type="submit" value="Compute" class="Buttons" >';
	echo '</form>';


## Compute the Shapley values
//  Now, we have posted our data to ourselves. Note that we're still inside the else{} part of the loop
// All that remains is to do the actual calculations.

$i = 0;

/*

Reminder

numPlayers: number of players
setOfPlayers: Numbers from 1 to n
powerSet: The power set of the above. All the different possible combinations.
getCoalitionNum: The value of coalitions 1 through 2^#
*/


$i = 0;

for($n = 0; $n<$numPlayers; $n++){
	$Impact[$n] = 0;
}

foreach($powerSet as $set){
	// in_array() function: will be useful. https://www.php.net/manual/es/function.in-array.php	

	$size = sizeof($set);
	if($size !=0){
		foreach($set as $player){
			
			$marginalImpact =  $getCoalitionNum[$i] - $getCoalitionNum[$i - pow(2,($player-1) ) ];
			$Impact[$player-1] += $marginalImpact / choose($size-1,$numPlayers-1);
		}
	}

	$i++;

}

echo 	'<p>';
for($n=1; $n <=$numPlayers; $n++){
	echo "The Shapley value of player ".$n." is: ".$Impact[$n-1]/$numPlayers."</br>";
}
echo 	'</p>';

} // this is the end of the else{} part of the "have we posted any data to ourselves yet" question. Only executes if we indeed have.


## More HTML flavor
//echo '<h3>Also of interest:</h3>
//	<a href="url">Shapley value resources</a> </br>'
echo 	'<p>';
echo 	'<br /></br>';
echo	'<a href="https://nunosempere.github.io/">Other things I have done</a> </br>';
echo 	'</p>';
echo '</body>';
echo '</html>';


## Functions

function powerSet($array){
	
	$results = array(array());
	foreach($array as $element){
		foreach($results as $combination){
			array_push($results, array_merge(array($element), $combination));
		}
	}
	return $results;
	// https://docstore.mik.ua/orelly/webprog/pcook/ch04_25.htm
}



function setToString($set){
	
	$size = sizeof($set);
	
	if($size == 0){
	
		return "{}";
	
	}else{
		$return = "{";
		for($i=0; $i<$size-1; $i++){
			$return .= $set[$i].", ";
		}
		$return .= $set[$i]."}";
		return $return;
	}
}

function factorial($n){
	if($n == 0){
		return 1;
	}else{

		$f = 1;
		for($i=$n; $i>=1;$i--){
			$f *= $i;
		}
		return $f;
	}	
}

function choose($m, $n){
	// Choose n objects among m choices
	return factorial($n) / (factorial($m)*factorial($n-$m));

}

?>
