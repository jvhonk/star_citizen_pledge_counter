<?php session_start(); 		

if ($_POST){

$_SESSION['tijdsduur'] = $_POST['timeframe'];
$_SESSION['refresh'] = $_POST['refresh'];
$_SESSION['sqltimeframe'] = time() - $_SESSION['tijdsduur'];
}

if (!$_SESSION['sqltimeframe'] ){

$_SESSION['tijdsduur'] = "43200";
$_SESSION['refresh'] = "300";
$_SESSION['sqltimeframe'] = time() - $_SESSION['tijdsduur'];
}
?>

<html>
<head><title>Star Citizen Pledge Counter</title>
<meta http-equiv="Pragma" content="no-cache">
<link rel="stylesheet" type="text/css" href="style.css">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>

<?php if ($_SESSION['refresh'] != "never"){
echo '<META HTTP-EQUIV="refresh" CONTENT="'. $_SESSION['refresh'].'">';}
include "analyticstracking.php"; 
include "simple_html_dom.php";
require_once './cron/database.php';
?>

</head>
<body>

<div id="kaart">		
<div id="ReloadThis">

<?php

date_default_timezone_set(timezone_name_from_abbr("EST"));

			$verbinding = new database ();
			$sql = "SELECT date,amount, differance FROM (SELECT * FROM pledges WHERE date > ".$_SESSION['sqltimeframe']." AND differance > 0 ORDER BY date DESC ) pledges WHERE date > ".$_SESSION['sqltimeframe']." ORDER BY date ASC;";
			//	$sql2 =  SELECT date > ".$_SESSION['sqltimeframe']." AND differance > 0 ORDER BY date DESC, sum(amount) FROM pledges GROUP BY DATE_FORMAT(date, '%Y-%m-%d');
			$test = $verbinding->queryDb ( $sql );
			
  ?>
  
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Time', 'Amount pledged'],	  
<?php	  
$i = 0; 
$overallpledged = 0;
$aantalrows = $test->num_rows ;
	while($row = mysqli_fetch_array($test)){ 
		
	$overallpledged = $overallpledged + $row['differance'];
		
		if ($i == ($aantalrows -1)){
		  echo "['".date("F j,H:i:s:T", $row['date']) . "', " . $row['differance']."]"; } 
		  
		  else {
		   echo "['".date("F j,H:i:s:T", $row['date']) . "', " . $row['differance']."],";
		  }
	 $i++;
	 } 
 ?>

]);    

        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, {
		width: 350, height: 200,
		legend: 'none',
		backgroundColor: {
		fill: 'none',
		'opacity': 100
		},
		
		colors: ['red','#004411','black'],
		hAxis: {
		textPosition: 'none',
        textStyle: {color: '#000', fontName: 'Arial'}, 
        gridlines: { color: 'black', count: 5} 
      },vAxis: {
	  
	  textStyle: {color: '#000', fontName: 'Arial', fontSize: 20}, 
        gridlines: { color: 'black', count: 6} 
      }
		});
      }
    </script>

<?php
function start(){

if (!isset($_SESSION['verschil'])) {$_SESSION['verschil'] = 0;}
if (!isset($_SESSION['totaal'])) {$_SESSION['totaal'] = 0;}
$_SESSION['goal'] = "10000000";
}

function pull($url){
$output = file_get_html($url)->find("div", 15);
return $output;
}

start();
$html = pull("http://www.robertsspaceindustries.com");

$pieces = explode("<strong>", $html->innertext);
echo '<span></span><div id="any-height">';

echo '<div id="version">v0.6</div><h3>Star Citizen Pledge Counter</h3><br><div class="middle">';
if (!isset($_SESSION['history'])) {$_SESSION['history'] = $pieces[1];}
$nu = preg_replace( '/\D+/', '', $pieces[1] );
$toen = preg_replace( '/\D+/', '', $_SESSION['history'] );    
$verschil = $nu - $toen;
$som = 100 - ((10000000-$nu) / 100000);
echo "<center><h1>".$pieces[1]."</h1></center>";

if ($verschil == 0 ) {
   if ($verschil < 0 ) {$_SESSION['totaal'] = $_SESSION['totaal'] - $verschil;
		echo '<div class="green"><strong>Last change : $0 </strong><br>'; } else {
echo '<div class="green"><strong>Last change : $'. $_SESSION['verschil'].'</strong><br>';	
}
echo '<strong>Stretch goal : ' . number_format($som).'%</strong><br>';
echo '<strong>This session : $' . $_SESSION['totaal'].'</strong><br>';
}
		else {
$_SESSION['verschil'] = $verschil;
$_SESSION['totaal'] = $_SESSION['totaal'] + $verschil;

echo '<div class="green"><strong>Last change : $' . number_format($verschil).'.</strong><br>';
echo '<strong>Stretch goal : ' . number_format($som).'%</strong><br>';
echo '<strong>This session : $' . $_SESSION['totaal'].'</strong><br>';	
	}
echo '<strong>Chart timeframe : $' . $overallpledged .'</strong><br><br>';
echo '<h2>Showing  last ';
if ($_SESSION['tijdsduur'] == "3600"){echo "1 Hour";}
if ($_SESSION['tijdsduur'] == "21600"){echo "6 Hours";}
if ($_SESSION['tijdsduur'] == "43200"){echo "12 Hours";}
if ($_SESSION['tijdsduur'] == "86400"){echo "24 Hours";}
if ($_SESSION['tijdsduur'] == "172800"){echo "2 Days";}
if ($_SESSION['tijdsduur'] == "604800"){echo "7 Days";}
echo '</h2>';

$_SESSION['history'] = $pieces[1];

 ?>

<center><div id="chart_div" style="width: 350px; height: 200px;"></div>


<div id="settingsTitle"><a href="#">Settings</a></div>
<div id="closesettingsTitle"><a href="#">X</a></div>

<script>

$(document).ready(function(){
 
        $("#settings").hide();
		$("#closesettingsTitle").hide();
		$("#settingsTitle").show();
 
    $('#settingsTitle').click(function(){
    $("#settings").slideToggle();
	$("#closesettingsTitle").show();
	$("#settingsTitle").toggle();
    });
	
	$('#closesettingsTitle').click(function(){
    $("#settings").slideToggle();
	$("#settingsTitle").slideToggle();
	$("#closesettingsTitle").hide();
    });
 
});


</script>

<div id="settings">
<form method="post" action="<?php echo $PHP_SELF;?>"> 
Chart Timeframe : 
<select name="timeframe">
<option value="3600" <?php if ($_SESSION['tijdsduur'] == "3600"){echo "selected";}?>>1 Hour</option>
<option value="21600" <?php if ($_SESSION['tijdsduur'] == "21600"){echo "selected";}?>>6 Hour</option>
<option value="43200" <?php if ($_SESSION['tijdsduur'] == "43200"){echo "selected";}?>>12 Hours</option>
<option value="86400" <?php if ($_SESSION['tijdsduur'] == "86400"){echo "selected";}?>>1 Day</option>
<option value="172800" <?php if ($_SESSION['tijdsduur'] == "172800"){echo "selected";}?>>2 Days</option>
<option value="604800" <?php if ($_SESSION['tijdsduur'] == "604800"){echo "selected";}?>>7 Days</option></select><br>
Refresh Rate : 
<select name="refresh">
<option value="60" <?php if ($_SESSION['refresh'] == "60"){echo "selected";}?>>1 Minute</option>
<option value="120" <?php if ($_SESSION['refresh'] == "120"){echo "selected";}?>>2 Minutes</option>
<option value="180" <?php if ($_SESSION['refresh'] == "180"){echo "selected";}?>>3 Minutes</option>
<option value="300" <?php if ($_SESSION['refresh'] == "300"){echo "selected";}?>>5 Minutes</option>
<option value="600" <?php if ($_SESSION['refresh'] == "600"){echo "selected";}?>>10 Minutes</option>
<option value="never" <?php if ($_SESSION['refresh'] == "never"){echo "selected";}?>>Never</option></select><br>
 <input class="timeframe" name="submit" value="submit" type="submit">
</form><br></center></div>

<?php 
echo '</div><div class="time">Updated ' . $tijdsduur .' '.date("H:i:s , T") . '</div></div>'; 

unset ($pieces);
$html->clear();
clearstatcache();
?>

<style>


</style>

</div>	
</div>
</div>

</body>
</html>