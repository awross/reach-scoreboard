<?php
    //takes in a file name to parse and find gamertags, returns array of all gamertags
    function grab_data($datafile=""){
		$gtag = array();
        $f=file($datafile);
        $cnt=count($f);
        for($i=0;$i<$cnt;$i++){
            if(preg_match("/<a class=\"fc-gtag-link\"/", $f[$i])){
                    $arr=explode(">", $f[$i]);
                    $name=explode("<", $arr[1]);
                    array_push($gtag, $name[0]);
            }	
        }
        return $gtag;
    }
    
    $gamer="Mr%20Jefferson120";
    $urlarr[]="http://www.bungie.net/Stats/Reach/Default.aspx?player=".$gamer."";	//overview
    $urlarr[]="http://www.bungie.net/Stats/Reach/CareerStats/playlists.aspx?player=".$gamer.""; //playlist url
    $urlarr[]="http://www.bungie.net/Stats/Reach/CareerStats/maps.aspx?player=".$gamer.""; //maps url
    $urlarr[]="http://www.bungie.net/Stats/Reach/CareerStats/weapons.aspx?player=".$gamer.""; //weapons
    $urlarr[]="http://www.bungie.net/Stats/Reach/Heatmaps.aspx?player=".$gamer.""; //heatmaps
    $urlarr[]="http://www.bungie.net/Stats/Reach/Challenges.aspx?player=".$gamer.""; //challenges
    $urlarr[]="http://www.bungie.net/Stats/Reach/PlayerGameHistory.aspx?player=".$gamer.""; //previous games
    //print_r($urlarr);
    //$test=file_get_contents("http://www.bungie.net/Stats/Reach/Default.aspx?player=Mr%20Jefferson120");
    //echo $test;
//    $string = file_get_contents("http://www.bungie.net/stats/reach/Playergamehistory.aspx?player=HecklingFext");
//	echo $string;
//gtag now has all gamertags from data.txt
?>
