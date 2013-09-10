<?php
    //takes in a file name to parse and find gamertags, returns array of all gamertags
    function grab_data($datafile=""){
        $f=file($datafile);
        $cnt=count($f);
        for($i=0;$i<$cnt;$i++){
            if(preg_match("/<a class=\"fc-gtag-link\"/", $f[$i])){
                    $arr=explode(">", $f[$i]);
                    $name=explode("<", $arr[1]);
                    array_push($gtag[], $name[0]);
            }	
        }
        return $gtag;
    }
//gtag now has all gamertags from data.txt
?>
