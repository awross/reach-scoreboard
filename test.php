<?php
 

class Game
{
	public $id = "";
	public $place = "";
	public $date = "";
	public $score = "";
	public $spread = "";
	public $map = "";
	public $playlist = "";

	private $host = "localhost";
	private $user = "root";
	private $pass = "YOUR_PASSWORD_HERE";
	private $link = "";
	private $result = "";

	function __construct()
	{
		$link = @mysql_connect("localhost", "root", "YOUR_PASSWORD_HERE") or die('Could not connect: ' . mysql_error());
		@mysql_select_db('halo') or die('Could not select database');
	}

	function tableRow()
	{
		$string = "<tr>";
		$string .= "<td>" . $this->id . "</td>";
		$string .= "<td>" . $this->place . "</td>";
		$string .= "<td>" . $this->date . "</td>";
		$string .= "<td>" . $this->score . "</td>";
		$string .= "<td>" . $this->spread . "</td>";
		$string .= "<td>" . $this->map . "</td>";
		$string .= "<td>" . $this->playlist . "</td>";
		$string .= "</tr>";

		return $string;
	}

	function Exists()
	{
		$result = @mysql_query("SELECT * FROM games WHERE id='" . $this->id . "' LIMIT 1;");
		if (@mysql_num_rows($result) > 0) { return true; }
		else { return false; }
	}
	
	function Add()
	{
		if (!$this->Exists())
		{
			$sql  = "INSERT INTO games VALUES (";
			$sql .= "'" . $this->id . "', ";
			$sql .= "'" . $this->place . "', ";
			$sql .= "'" . $this->date . "', ";
			$sql .= "'" . $this->score . "', ";
			$sql .= "'" . $this->spread . "', ";
			$sql .= "'" . $this->map . "', ";
			$sql .= "'" . $this->playlist . "');";

			$result = @mysql_query($sql);
			if (!$result) {
				$message  = 'Invalid query: ' . mysql_error() . "\n";
				$message .= 'Whole query: ' . $query;
				die($message);
			}
		}
	}
}


/* update your path accordingly */
include_once 'simple_html_dom.php';
 
$search_term = "HecklingFext";
 
$url = "http://www.bungie.net/stats/reach/playergamehistory.aspx?player=" . $search_term;
 
$html = file_get_html($url);

$hitFirst = false;
$games = array();
$pacTimezone = new DateTimeZone('America/Vancouver');
$userTimezone = new DateTimeZone('America/New_York');

foreach($html->find('tr') as $element)
{
	if (!$hitFirst && $element->getAttribute('id') != "ctl00_mainContent_recentgames_ctl00__0")
	{
		continue;
	}
	else
	{
		$hitFirst = true;
	}

	$temp = new Game();

	foreach($element->find('p') as $piece)
	{
		switch($piece->getAttribute("class"))
		{
			case "outcome":
				if ($temp->id == "")
				{
					$link = $piece->first_child()->getAttribute("href");
					$pattern = '/gameid=(?P<id>\d+)/';
					preg_match($pattern, $link, $matches);
					$temp->id = $matches['id'];
				}
				break;
			case "place":
				if ($temp->place == "") { $temp->place = $piece->plaintext; }
				break;
			case "date":
				if ($temp->date == "")
				{
					$t_date = date_parse_from_format("n.d.Y g:i A", $piece->plaintext);
					$date_string = $t_date['year'] . "-" . $t_date['month'] . "-" . $t_date['day'] . " " . $t_date['hour'] . ":" . $t_date['minute'];
					$t_date = new DateTime($date_string, $pacTimezone);
					$offset = $userTimezone->getOffset($t_date);
					$temp->date = date('Y-m-d H:i:s', $t_date->format('U') + $offset);
				}
				break;
			case "score":
				if ($temp->score == "") { $temp->score = $piece->plaintext; }
				break;
			case "spread":
				if ($temp->spread == "") { $temp->spread = $piece->plaintext; }
				break;
			case "map":
				if ($temp->map == "") { $temp->map = $piece->plaintext; }
				break;
			case "playlist":
				if ($temp->playlist == "") { $temp->playlist = $piece->plaintext; }
				break;
			default:
				continue;
		}
	}

	if ( !$temp->Exists() ) { $temp->Add(); }
	else { break; }
}

//print_r($games);

?>
