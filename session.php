<?php
$link = @mysql_connect("localhost", "root", "YOUR_PASSWORD_HERE") or die('Could not connect: ' . mysql_error());
@mysql_select_db('halo') or die('Could not select database');
$sql = "INSERT INTO sessions VALUES ('', '', '" . date("Y-m-d H:i:s", ( time() - (60*60*4) )) . "');";
$result = @mysql_query($sql);
?>
