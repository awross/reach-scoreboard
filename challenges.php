<?php
require("challenge.php");

$chal = getChallenges("HecklingFext");
print_r($chal);

$week = "";
$game = "";
$day  = "";

foreach ($chal as $c)
{

	switch ($c->scope)
	{
		case "Week":
			$week .= $c->verbose() . "\n";
			break;
		case "Game":
			$game .= $c->verbose() . "\n";
			break;
		case "Day":
			$day .= $c->verbose() . "\n";
			break;
		default:
			break;
	}
}

echo $game . $day . $week;
?>
