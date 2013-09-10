<?php
echo "<html><head>";
echo "
<style type='text/css'>
	body {background-color:black; color:white; }
	.red {color:red; font-size:144;}
	.blue {color:blue; font-size:144;}
	.spreads {display:inline;}
</style>

<script type='text/javascript' src='jquery-1.4.3.min.js'></script>  
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
				url: 'jstest.php',
				data: 'name=John&location=Boston',
				success: function(msg){
					$('div.demo').html(msg);
				}
			});
		});
		window.setInterval('location.reload(true)',60000);
	});
</script>";
echo "</head><body>";

$table =  "<table id='main'>" . $table;
$table .= "<tr><td align='center' colspan='3'><button class='reset'>RESET</button></td></tr>";
$table .= "<tr><td align='center' colspan='3'><div class='demo'></div></td></tr>";

$table .=  "</table>";

echo $table;
echo "</body></html>";
?>
