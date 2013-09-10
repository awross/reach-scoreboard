<?php
include("test.php");
echo "<html><head>";
echo "
<style type='text/css'>
	body {background-color:black; color:white; }
	.red {color:red; font-size:144;}
	.blue {color:blue; font-size:144;}
	.spreads {display:inline;}
</style>

<script type='text/javascript' src='jquery-1.4.3.min.js'></script>  
<script src='jquery.jclock-1.2.0.js.txt' type='text/javascript'></script>
<script type='text/javascript'>
$(function($) {
	var options = {
        timeNotation: '24h',
        am_pm: false,
        fontFamily: 'Verdana, Times New Roman',
        fontSize: '106px',
        foreground: 'gray',
      }; 
   $('.jclock').jclock(options);
});
</script>


<script>
	$(document).ready(function() {
		$('.reset').click(function() {
			$.ajax({
				type: 'GET',
				url: 'session.php',
				data: 'name=John&location=Boston',
				success: function(msg){
					alert( 'Session Reset!' );
					window.location.reload();
				}
			});
		});
		window.setInterval('location.reload(true)',60000);
	});
</script>";
echo "</head><body>";
$link = @mysql_connect("localhost", "root", "YOUR_PASSWORD_HERE") or die('Could not connect: ' . mysql_error());
@mysql_select_db('halo') or die('Could not select database');

$wins = 0;
$loss = 0;
$thisSpread = 0;
$totalSpread = 0;
$lastSpread = 9999;
$lastResult = "";
$lastStreak = 0;

$lastTime = "";
$table = "";
$games = array();

$result = @mysql_query("SELECT * FROM sessions ORDER BY tstamp DESC LIMIT 1;");
if (@mysql_num_rows($result) > 0)
{
	$row = mysql_fetch_assoc($result);
	$lastTime = date( 'Y-m-d H:i:s', (strtotime($row['tstamp']) - (60*60*0)) );
	$sql = "SELECT * FROM games WHERE date > '" . $lastTime . "' ORDER BY date ASC;";//echo $sql;
	$result = @mysql_query($sql);
	if (@mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_assoc($result))
		{
			$temp = new Game();
			$temp->id = $row['id'];
			$temp->place = $row['place'];
			$temp->date = $row['date'];
			$temp->score = $row['score'];
			$temp->spread = $row['spread'];
			$temp->map = $row['map'];
			$temp->playlist = $row['playlist'];
			array_push($games, $temp);
			//$table .= $temp->tableRow();
		}
	}
}
else
{
	echo "ERROR";
}

$not_count = array(
	"Firefight",
	"Custom Game",
	"Score Attack"
);

$count = 0;
print_r($games);
foreach($games as $game)
{
	if (!in_array($game->playlist, $not_count))
	{
		if (strtotime($game->date) > strtotime($lastTime))
		{
			//echo $lastResult . $lastStreak . "<br />";
			$lastSpread = $game->spread;

			if ($game->playlist == "Rumble Pit")
			{
				if ($game->place < 4)
				{
					$wins++;
					if ($lastResult == "W")
					{
						$lastStreak++;
					}
					else
					{
						$lastStreak = 1;
						$lastResult = "W";
					}
				}
				else
				{
					$loss++;
					if ($lastResult == "L")
					{
						$lastStreak++;
					}
					else
					{
						$lastStreak = 1;
						$lastResult = "L";
					}
				}
			}
			else
			{
				if ($game->place == 1)
				{
					$wins++;
					if ($lastResult == "W")
					{
						$lastStreak++;
					}
					else
					{
						$lastStreak = 1;
						$lastResult = "W";
					}
				}
				else
				{
					$loss++;
					if ($lastResult == "L")
					{
						$lastStreak++;
					}
					else
					{
						$lastStreak = 1;
						$lastResult = "L";
					}
				}
			}

			$thisSpread += $game->spread;
		}
		$totalSpread += $game->spread;
	}
}

require("challenge.php");

$chal = getChallenges("HecklingFext");

$week = "";
$game = "";
$day  = "";

foreach ($chal as $c)
{

	switch ($c->scope)
	{
		case "Week":
			$week .= "<tr>" . $c->tverbose() . "</tr>\n";
			break;
		case "Game":
			$game .= "<tr>" . $c->tverbose() . "</tr>\n";
			break;
		case "Day":
			$day .= "<tr>" . $c->tverbose() . "</tr>\n";
			break;
		default:
			break;
	}
}
$chal_tbl = "<table padding='10px'>" . $game . $day . $week . "</table>";

$table =  "<table id='main'>" . $table;
$table .= "<tr><td align='center' colspan='3'><button class='reset'>RESET</button></td></tr>";
$table .= "<tr><td align='left' width='200'>Wins</td><td align='center'><h3>" . $lastResult . $lastStreak . "<h3></td><td align='right' width='200'>Loss</td></tr>";
$table .= "<tr><td align='left' width='200'><h3 class='blue'>" . $wins . "</h3></td><td class='spreads' width='300' align='center'><h4>" . $thisSpread . " (" . ($lastSpread == 9999 ? "-" : $lastSpread) . ") </h4></td><td><h3 class='red' align='right' width='200'>" . $loss . "</h3></td></tr>";
//$table .= "<tr><td align='center' colspan='3' style='color:red;'>(" . $totalSpread . ")</td></tr>";
$table .= "<tr><td align='center' colspan='3'>" . $chal_tbl . "</td></tr>";
$table .= "<tr><td align='center' colspan='3'><div class='jclock'></div></td></tr>";

$table .=  "</table>";

echo $table;
echo "</body></html>";
?>
