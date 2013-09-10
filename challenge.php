<?php
include_once 'simple_html_dom.php';

class Challenge
{
	public $title	= "";
	public $desc	= "";
	public $obj		= "";
	public $type	= "";
	public $scope	= "";
	public $reward	= "";
	public $done	= "";
	public $total	= "";

	private $host = "127.0.0.1";
	private $user = "root";
	private $pass = "YOUR_PASSWORD_HERE";
	private $link = "";
	private $result = "";

	function __construct()
	{
		$link = @mysql_connect($this->host, $this->user, $this->pass) or die('Could not connect: ' . mysql_error());
		@mysql_select_db('halo') or die('Could not select database');
	}
	
	
	public function tverbose()
	{
		$s  = "<tr border='1'>";
		$s .= "<td align='right' width='5%'>" . $this->scope . ":</td>";
		/*
		 *	Returns just the first letter
		 *	------------------------------
		 *	          LEGEND
		 *	-----------------------------
		 *	R	-	any game mode in Reach
		 *
		 *	C	-	Campaign
		 *	E	-	Campaign, Easy
		 *	N	-	Campaign, Normal
		 *	H	-	Campaign, Heroic
		 *	L	-	Campaign, Legendary
		 *	
		 *	F	-	Matchmaking, Firefight
		 *	M	-	Matchmaking, Multiplayer
		 *	-----------------------------
		 */
		$s .= "<td align='left' width='5%'>" . substr($this->type,0,1) . "</td>";
		$s .= "<td align='center' width='60%'>" . $this->obj . "</td>";
		$s .= "<td align='right' width='5%'>" . $this->done . " /</td>";
		$s .= "<td align='left' width='5%'> " . $this->total . "</td>";
		$s .= "<td align='right' width='10%'> &nbsp; &nbsp; &nbsp;" . $this->reward . "cR</td>";
		$s .= "</tr>";

		$s = $this->reward == "" ? "" : $s;
		if ($this->type == "Multiplayer" || $this->type == "Reach")
		{
			return $s;
		}
		else
		{
			return "";
		}
	}
	public function verbose()
	{
		$s = str_pad($this->scope, 5, " ") . "- " . str_pad(substr($this->type,0,1), 4, " ") . str_pad(($this->done == $this->total ? "\/\/\/\/DONE\/\/\/\/" : $this->obj), 75, "-", STR_PAD_BOTH);
		//$s .= str_pad($this->title, 20, " ");
		$s .= str_pad($this->done, 4, " ", STR_PAD_LEFT) . " / " . str_pad($this->total, 5, " ");
		$s .= str_pad($this->reward . "cR", 10, " ", STR_PAD_LEFT);

		return $s;
	}

	function Exists()
	{
		$result = @mysql_query("SELECT * FROM challenges WHERE obj='" . $this->obj . "' AND date > '" . date( "Y-m-d H:m:s", (time() - (60*60*24*1) + 30) ) . "' LIMIT 1;");
		if (@mysql_num_rows($result) > 0) { return true; }
		else { return false; }
	}
	
	function Add()
	{
		if (!$this->Exists())
		{
			$sql  = "INSERT INTO challenges VALUES (";
			$sql .= "'', ";
			$sql .= "'" . date('Y-m-d H:i:s') . "', ";
			$sql .= "'" . $this->title . "', ";
			$sql .= "'" . $this->desc . "', ";
			$sql .= "'" . $this->obj . "', ";
			$sql .= "'" . $this->type . "', ";
			$sql .= "'" . $this->scope . "', ";
			$sql .= "'" . $this->reward . "', ";
			$sql .= "'" . $this->done . "', ";
			$sql .= "'" . $this->total . "');";

			$result = @mysql_query($sql);
			if (!$result)
			{
				$message  = 'Invalid query: ' . mysql_error() . "\n";
				$message .= 'Whole query: ' . $query;
				die($message);
			}
		}
		/*
		else
		{
			$sql  = "UPDATE challenges ";
			$sql .= "SET done = '" . $this->done . "' ";
			$sql .= "WHERE challenges.desc = '" . $this->desc . "' ";
			$sql .= "AND date > '" . date("Y-m-d H:m:s", (time() - (60*60*24*1))) . "' LIMIT 1;";
			echo $sql . "\n\n";

			$result = @mysql_query($sql);
			if (!$result)
			{
				$message  = 'Invalid query: ' . mysql_error() . "\n";
				$message .= 'Whole query: ' . $query;
				die($message);
			}
		}
		*/
	}
}

function getChallenges($user="HecklingFext")
{
	$chal = array();

	$f=file_get_html("http://www.bungie.net/stats/reach/challenges.aspx?player=" . $user);
	foreach($f->find('ul.challengesList > li') as $e)
	{
		$c = new Challenge();

		// Title
		$regex_pattern = "/<h5>(.*)<\/h5>/";
		preg_match($regex_pattern,$e,$matches);
		$c->title = $matches[1];

		// Description
		$regex_pattern = "/<p class=\"description\">(.*)<\/p>/";
		preg_match($regex_pattern,$e,$matches);
		$c->desc = $matches[1];

		if( preg_match("/(.*) in any game mode in Reach(.*)/",$c->desc,$matches) )
		{
			$c->obj = $matches[1] . $matches[2];
			$c->type = "Reach";
			$c->scope = "Day";
		}
		else if( preg_match("/(.*) in the same match in (.*) Matchmaking(.*)/",$c->desc,$matches) )
		{
			$c->obj = $matches[1] . $matches[3];
			$c->type = ucfirst($matches[2]);
			$c->scope = "Game";
		}
		else if( preg_match("/(.*) in a (.*) Matchmaking game(.*)/",$c->desc,$matches) )
		{
			$c->obj = $matches[1] . $matches[3];
			$c->type = ucfirst($matches[2]);
			$c->scope = "Game";
		}
		else if( preg_match("/(.*) a (.*) Matchmaking game(.*)/",$c->desc,$matches) )
		{
			$c->obj = $matches[1] . " " . $matches[2] . $matches[3];
			$c->type = ucfirst($matches[2]);
			$c->scope = "Game";
		}
		else if( preg_match("/(.*) in (.*) Matchmaking(.*)/",$c->desc,$matches) )
		{
			$c->obj = $matches[1] . $matches[3];
			$c->type = ucfirst($matches[2]);
			if (count($chal) == 0)
			{
				$c->scope = "Week";
			}
			else
			{
				$c->scope = "Day";
			}
		}
		else if( preg_match("/(.*) in the Campaign(.*)/",$c->desc,$matches) )
		{
			$c->obj = $matches[1] . $matches[2];
			$c->type = "Campaign";
			if (count($chal) == 0)
			{
				$c->scope = "Week";
			}
			else
			{
				$c->scope = "Day";
			}
		}
		else if( preg_match("/(.*) on any Campaign mission(.*)/",$c->desc,$matches) )
		{
			$c->obj = $matches[1] . $matches[2];
			$c->type = "Campaign";
			if (count($chal) == 0)
			{
				$c->scope = "Week";
			}
			else
			{
				$c->scope = "Day";
			}
		}
		else if( preg_match("/(.*) Legendary(.*)\(LASO\)(.*)/",$c->desc,$matches) )
		{
			$c->obj = $matches[1] . " LASO" . $matches[3];
			$c->type = "Legendary";
			if (count($chal) == 0)
			{
				$c->scope = "Week";
			}
			else
			{
				$c->scope = "Day";
			}
		}
		else if( preg_match("/Complete (.*), without dying, on (.*) with (.*)/",$c->desc,$matches) )
		{
			$c->obj = $matches[1] . " without dying with " . $matches[3];
			$c->type = $matches[2];
			if (count($chal) == 0)
			{
				$c->scope = "Week";
			}
			else
			{
				$c->scope = "Day";
			}
		}
		else if( preg_match("/Complete (.*), without dying,(.*)/",$c->desc,$matches) )
		{
			$c->obj = $matches[1] . " without dying" . $matches[2];
			$c->type = "Campaign";
			if (count($chal) == 0)
			{
				$c->scope = "Week";
			}
			else
			{
				$c->scope = "Day";
			}
		}
		else
		{
			$regex_pattern = "/(.*) on ([a-zA-Z]+)(.*)/";
			preg_match($regex_pattern,$c->desc,$matches);
			$c->obj = $matches[1] . $matches[3];
			$c->type = ucfirst($matches[2]);
			if (count($chal) == 0)
			{
				$c->scope = "Week";
			}
			else
			{
				$c->scope = "Day";
			}
		}
		
		// Weed out extraneous words that are clear from this->scope| eg: today
		$w_arr = array();
		array_push($w_arr, "today");
		array_push($w_arr, "at least");
		array_push($w_arr, "in Campaign");
		array_push($w_arr, "or harder");
		array_push($w_arr, "from the Needler or Needle Rifle");

		foreach($w_arr as $w)
		{
			$c->obj = str_replace((" " . $w), "", $c->obj);
		}

		// Credit / Reward
		$regex_pattern = "/<p>(.*)cR<\/p>/";
		preg_match($regex_pattern,$e,$matches);
		$c->reward = $matches[1];
		
		// Total needed/amount done
		foreach($e->find('div.barContainer') as $x)
		{
			$regex_pattern = "/<p>(.*)<\/p>/";
			preg_match($regex_pattern,$x,$matches);
			$a = explode('/', $matches[1]);
			$c->done = $a[0];
			$c->total = $a[1];
			break;
		}

		array_push($chal, $c);
	}

	return $chal;
}
?>
