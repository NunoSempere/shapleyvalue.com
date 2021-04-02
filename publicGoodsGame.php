
<?php

/*

*	File: /shapleyvalue.com/publicGoodsGame.php
*	By: Nuño Sempere
* 	Date: 23 Sept 2019
* 	 
*	This file applies the Shapley value to a public goods game with 6 players.

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

$thisFile = "publicGoodsGame.php";


## Power set of all players

$numPlayers = 6;
	## Initially, the number of players is 6.
for($n = 1; $n <= $numPlayers; $n++){
	$setOfPlayers[$n] = $n;
}

$powerSet = powerSet($setOfPlayers);

$i = 0;
foreach($powerSet as $set){
	
	$powerSetAsStrings[$i] = setToString($set);
	$i++;
}

## Check if the thing has been posted

$getContributionPlayer = $_POST['ContributionPlayer']?? -1;

$getMultiplier = $_POST['Multiplier'] ?? -1;

if($getContributionPlayer == -1){

	## Get the contributions of each player to the common pot
	echo '<form action='.$thisFile.' method = "post">';
	echo '<div class="form">';

	echo '<input type="hidden" name="numplayers" value='.$numPlayers.' />';
	$i = 0;

	for($i = 0; $i<$numPlayers; $i++){
		echo '<p>';
			echo '<label>Contribution of Player #'.($i+1).': &nbsp</label>';
			echo '<input type="number" name="ContributionPlayer['.($i).']" value = "0" class = "Box">';
		echo '</p>';
	}
	echo '<p></br></p>';
	echo '<p>';
		echo '<label>Multiplier = &nbsp </label>';
		echo '<input type="number" name="Multiplier" value = "0" step="0.01" class = "Box">';
	echo '</p>';
	
	echo '</br>';

	echo '</div>';
		echo '<input type="submit" value="Compute" class="Buttons" >';
	echo '</form>';

}else{


	echo '<form action='.$thisFile.' method = "post">';
	echo '<div class="form">';

	echo '<input type="hidden" name="numplayers" value='.$numPlayers.' />';
	$i = 0;

	for($i = 0; $i<$numPlayers; $i++){
		echo '<p>';
			echo '<label>Contribution of Player #'.($i+1).': &nbsp</label>';
			echo '<input type="number" name="ContributionPlayer['.($i).']" value ='.$getContributionPlayer[($i)].' class = "Box">';
		echo '</p>';
	}

	echo '<p></br></p>';
	echo '<p>';
		echo '<label>Multiplier = &nbsp </label>';
		echo '<input type="number" name="Multiplier" value ='.$getMultiplier.' step = "0.01" class = "Box">';
	echo '</p>';
	
	echo '</br>';

	echo '</div>';
		echo '<input type="submit" value="Compute" class="Buttons" >';
	echo '</form>';

	## Now, the Shapley value in a public good game is simply going to be contribution*multiplier
	## The classical reward is simply aggregate*multiplier/number of players.
	## Let D = Shapley value - Classical reward
	## Then it would be interesting to check what happens when the payout is
	## P = Classical Reward + Alpha*D,
	## Where alpha ranges from 0 to 1.
	## I think that just comparing alpha = 0, alpha = 1/3, alpha = 2/3, (alpha = 1) would be interesting.

	$sumContributions = array_sum($getContributionPlayer);
	
	echo '<p>';
		echo 'Sum of the contributions = &nbsp'.$sumContributions;
	echo '</p>';
	echo '<p>';
		echo 'Sum*Multiplier = &nbsp'.($sumContributions*$getMultiplier);
	echo '</p>';
	echo '<p>';
		$payout = ($sumContributions*$getMultiplier/$numPlayers);
		echo 'Payout = Sum*Multiplier/ Number of Players = &nbsp'.$payout;
	echo '</p>';
	
	## What is the difference between the payout and the Shapley value?

	echo '<div class="centeredExample">';
	echo '<p>';
	echo "<h3>For each player, what is the difference between the payout and the Shapley value? </h3>";
	for($i = 0; $i<$numPlayers; $i++){
		echo '<p>';
			echo 'Difference for Player #'.($i+1).'&nbsp = '.($getMultiplier*$getContributionPlayer[$i]-$payout);
		echo '</p>';
	}
	echo '</p>';
	echo '</div>';
}
## Footer
echo '<p><a href="/">Go back</a></p></br>';

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
