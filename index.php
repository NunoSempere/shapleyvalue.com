<?php

/*

*	File: /shapleyvalue/calculator
*	By: Nuño Sempere
* 	Date: 23 Sept 2019
* 	Last modified: 2 Apr 2021
*	This file calculates the Shapley value of every member of a given coalition.

*/


## HTML Flavor 

echo '<html>';

echo '<head>
	<title>Shapley Value Calculator</title>
	<meta name="copyright" content="Nuño Sempere López-Hidalgo">
	<meta name="keywords" content="Shapley Value, Shapley value, calculate Shapley value, calculate Shapley value online free, Shapley value program">
	<meta name="description" content="Shapley value calculator">
	<script async defer data-domain="shapleyvalue.com" src="https://plausible.io/js/plausible.js"></script>
	<link rel="stylesheet" href="CSS/main.css" type="text/css">'; 
	// script is the reference to plausible.io web tracking
	// link rel is the reference to the CSS style sheet.'
echo '</head>';

echo '<body>';

echo '<h1> Shapley Value Calculator </h1>';


## Pointer to this file

$thisFile = "index.php";

## Decide the Number of Players
 
$numPlayers = $_POST["numplayers"] ?? 3; 
	// It sees what number of players the user has selected.
	// If it's the first time the user comes to the webpage, the default will be 3

## Prints the current example:
$example = $_GET["example"] ?? -1;

echo '<div class="centeredExample">';
echo "<p>";
switch ($example) {
    case 1:
        echo "Example 1: Alice and Bob and both necessary to produce something which has value 1500. Alice is player 1, Bob is player 2.";
        $numPlayers = 2;
	$getCoalitionNum = array(0,0,0,1500);
	break;
    case 2:
        echo "Example 2: Alice and Bob are each individually responsible for two different projects, each of which has value 1000. Alice is player 1, Bob is player 2.";
	$getCoalitionNum = array(0,1000,1000,2000);
        $numPlayers =2;
        break;
    case 3:
	echo "Example 3: Newton and Leibniz invented calculus at the same time. It has a value of 100, in arbitrary units. Assumption: Nobody else could have invented calculus. Newton is player 1, Leibniz is player 2";
	$getCoalitionNum = array(0,100,100,100);
        $numPlayers =2;
        break;
    case 4:
	echo "Example 4: Netwon invented Calculus. Leibniz, mad with envy, pretended that he also invented calculus at the same time. Newton is player 1. Lebniz is player 2.";
	$getCoalitionNum = array(0,100,0,100);
        $numPlayers =2;
        break;
    case 5:
	echo "Example 5: Suppose that AMF will spend $1M on a net distribution. As a result of AMF’s commitment, the Gates Foundation contributes $400,000. If AMF had not acted, Gates would have spent the $400,000 on something else, half as valuable. AMF is player 1, Gates is player 2.";
	$getCoalitionNum = array(0,1000000,200000,1400000);
        $numPlayers =2;
        break;
    case 6:
	echo "Example 6: Suppose that AMF commits $1M to a net distribution. But if AMF had put nothing in, DFID would instead have committed $500,000 to the net distribution. Now, DFID commits that money to something half as valuable. AMF is player 1, DFID is player 2.";
	$getCoalitionNum = array(0,1000000,500000,1250000);
        $numPlayers =2;
        break;
    case 7:
	echo "Example 7: 7 people boil a goat in their mother's milk, independently and at the same time. According to the Kabbalah, this has terrible implications: -1000 value is lost. Suppose that all the damage is done once the first deed is done.";
        $numPlayers =7;
	$getCoalitionNum = array(0);
	for($i = 1; $i<128; $i++){
		array_push($getCoalitionNum, -100);
	}
        break;
    case 8:
	echo "Example 8: Suppose that there was a position in an EA org, for which there were 6 qualified applicants which are otherwise 'idle'. In arbitrary units, the person in that position in that organization can produce an impact of 100 utility. The organization is player 1, applicants are players 2-7.";
        $numPlayers =7;
	$getCoalitionNum = array(0);
	for($i = 1; $i<128; $i++){
		if($i<=64){
			array_push($getCoalitionNum, 0);
		}else{
			array_push($getCoalitionNum, 100);
		}
	}
        break;
    case 9:
	echo "Example 9: A small Indian state with 10 million inhabitants spends $60 million to vaccinate 30% of their population. An NGO which would otherwise be doing something really ineffective, comes in, and by sending reminders, increases the vaccination rate to 35%. They do this very cheaply, for $100,000. The government is player 1, the indian state is player 2. Exercise: What if, instead, the NGO would have done something equally valuable?";
        $numPlayers =2;
	$getCoalitionNum = array(0,3000000,0,3500000);
        break;
    case 10:
	echo "Example 10: Same as Example 9, but now there are 6 government subagencies, each of which we consider as a distinct agent. The NGO is player 1, government agencies are players 2-7.";
        $numPlayers =7;
	$getCoalitionNum = array(0);
	for($i = 1; $i<128; $i++){
		if($i<126){
			array_push($getCoalitionNum, 0);
		}else if($i==126){
			array_push($getCoalitionNum, 3000000);
		}else{
			array_push($getCoalitionNum, 3500000);
		}
	}
	break;
    default:
        $example = -1;
	break;

}
echo "</p>";
echo "</div>";
echo "</br>";
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


## Forms for the value of the coalition. If there is an example, use that.

if($example==-1){
	$getCoalitionNum = $_POST['CoalitionNum']?? 0;
}

if($getCoalitionNum == 0){
	//That is, if we haven't yet posted anything to ourselves regarding the size of the coalition
	// Or if we're not in an example.
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

} // this is the end of the else{} part of the "have we posted any data to ourselves yet, or are we in an example" question. Only executes if we indeed have.

echo '</br>';

echo '<a href="/examples.html">List of examples</a></br>';
echo '<a href="?example='.rand(1,10).'">Random example</a></br>';

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
