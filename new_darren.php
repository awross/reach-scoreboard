<?php
    include_once'simple_html_dom.php';
    $error="Gamertag was not entered";
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

    function get_stats($gamer=""){
        //spaces in username needs to be represented by '%20' otherwise query wont work
        if($gamer=="")
            $gamer="Mr Jefferson120";//for testing purposes
        
        //I believe that the space is the only warning char allowed in an xbox live gamertag that we have to adjust for
        $urlencode=explode(" ", $gamer);
        $urlsafe=implode("%20", $urlencode);
        $gamer=$urlsafe;
        
        $playerStats=grab_overview($gamer);//[0]=>kills(KD) 1=>[streak]
        $mapStats=grab_map($gamer); //[0] Bestmapname(KD), Worstmapname(KD) 
	$wepStats=grab_weapons($gamer);//grabs best wep with over 50 kills [i]=>name [i+1]=>kills[i+2]=>K/d
        grab_gamehistory($gamer);
        $urlarr[]="http://www.bungie.net/Stats/Reach/Heatmaps.aspx?player=".$gamer.""; //heatmaps
        $urlarr[]="http://www.bungie.net/Stats/Reach/Challenges.aspx?player=".$gamer.""; //challenges
        
        
        
    }
    //grabs overall kills, k/d , and current streak from overview page & returns in form of an array
    function grab_overview($gamer=""){
        if($gamer==""){
            echo "Error";
        }
        $url="http://www.bungie.net/Stats/Reach/Default.aspx?player=".$gamer."";	//overview
        $gamerstats= array();
        $test2=file($url);//for overview
        $cnt=count($test2);
        $streak=0;
        for($i=0;$i<$cnt;$i++){ //loop might not be needed
            if(preg_match("/<span id=\"ctl00_mainContent_matchmakingKillsLabel\"/", $test2[$i])){
                $arr=explode(">", $test2[$i]);
                $stats=explode("<", $arr[4]);//includes <strong>before so it must be adjusted from 1 to 4
                array_push($gamerstats, $stats[0]);
            }   //^gets kills with K/d
            elseif(preg_match("/class=\"gamePanel blue\"/",$test2[$i]) || preg_match("/class=\"gamePanel blue last\"/",$test2[$i])){
                if($streak<0){
                    $streak=0;
                }
                $streak++;
            }
            elseif(preg_match("/class=\"gamePanel red\"/", $test2[$i]) || preg_match("/class=\"gamePanel red last\"/", $test2[$i])){
                if($streak>0){//sets streak to 0
                    $streak=0;
                }
                else //makes streak more negative if already negative
                    $streak--;
            }
        }
        array_push($gamerstats, $streak);
        return $gamerstats;
    }
    //gets best and worst map into an array and returns array
    function grab_map($gamer=""){
        if($gamer==""){
            echo $error;
        }
        $url="http://www.bungie.net/Stats/Reach/CareerStats/maps.aspx?player=".$gamer."&vc=3"; //maps url
        $gamerstats=array();
        $file=file($url);
        $cnt=count($file);
	$maparr=array();
	$bestmap=-1;//invalid K/d's
	$worstmap=100;
	$wpos;
	$bpos;
	$count=0;
        for($i=0;$i<$cnt;$i++){
	    if(preg_match("/<h4><strong>/", $file[$i])){
		$m=explode(">", $file[$i]);
		$t=explode("<", $m[2]);
		array_push($maparr, $t[0]);$count++;
	    }
            if(preg_match("/<strong>K\/D:/", $file[$i])){
                $kd=explode(":", $file[$i]);
                $temp=explode(">", $kd[1]);
                $k2d=explode("<", $temp[2]);
                if($k2d[0]<$worstmap){
		    $worstmap=$k2d[0];
		    $wpos=$count-1;
		}
		elseif($k2d[0]>$bestmap){
		    $bestmap=$k2d[0];
		    $bpos=$count-1;
		}
            }
        }
	$bmapwKd=$maparr[$bpos] ." (".$bestmap.")";
	$wmapwKd=$maparr[$wpos] ." (".$worstmap.")";
	$returnarr=array();
	array_push($returnarr, $bmapwKd);
	array_push($returnarr, $wmapwKd);
	return $returnarr;
    }
    function grab_playlist($gamer=""){
        if($gamer==""){
            echo $error;
        }
        $url="http://www.bungie.net/Stats/Reach/CareerStats/playlists.aspx?player=".$gamer.""; //playlist url
        //not sure what we wanna grab from here because it would require user input for playlist
    }
    function grab_weapons($gamer=""){
	if($gamer==""){
            echo $error;
        }
	$url="http://www.bungie.net/Stats/Reach/CareerStats/weapons.aspx?player=".$gamer."&vc=3&sort=3"; //weapons
        $returnarr=array();
	$file=file($url);
	$cnt=count($file);
        $temp;
        $i=0;
        $found=false;
        while($i<$cnt &&!$found){
            if(preg_match("/<h4>/", $file[$i]) && !preg_match("/id/", $file[$i])&&!preg_match("/Arena/", $file[$i]) &&!preg_match("/class=/", $file[$i])){
                    $tempex=explode(">",$file[$i]);
                    $tempex2=explode("<", $tempex[1]);
                    $temp=$tempex2[0];
            }
            if(preg_match('/class="kills"/', $file[$i])){
                $tkills=explode(">", $file[$i+2]);
                $kills=explode("<", $tkills[1]);
                if(!preg_match("/id=/", $file[$i+2]) && $kills[0]>50){
                    array_push($returnarr, $temp);
                    array_push($returnarr, $kills[0]);
                }
                else{
                    $temp=0;
                }
            }
	    if(preg_match("/KD on/", $file[$i])){
                if(!preg_match("/id=/", $file[$i+2]) && $temp){
                    array_push($returnarr, $file[$i+2]);
                    $found=true;
                }
	    }
            $i++;
	}
        return $returnarr;
    }
    function grab_gamehistory($gamer=""){
	if($gamer==""){
            echo $error;
        }
	$url="http://www.bungie.net/Stats/Reach/PlayerGameHistory.aspx?player=".$gamer.""; //previous games\
	$file=file($url);
	$cnt=count($file);
	$i=0;
	while($i<$cnt){
	    if(preg_match('/class="score"/', $file[$i])){
		$t=explode(">", $file[$i]);
		$score=explode("<", $t[1]);
		if($score[0] !="Score"){
		    echo $score[0];
		}
	    }
	    $i++;
	}
    }
    echo get_stats("Mr Jefferson120");
    
?>