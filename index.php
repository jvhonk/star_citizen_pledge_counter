<?php session_start(); 		

if ($_POST){

$_SESSION['tijdsduur'] = $_POST['timeframe'];
$_SESSION['refresh'] = $_POST['refresh'];
$_SESSION['sqltimeframe'] = time() - $_SESSION['tijdsduur'];
$_SESSION['timeinterval'] = $_POST['timeinterval'];
$_SESSION['pledgemode'] = $_POST['pledgemode'];
}

if (!$_SESSION['timeinterval']){
$_SESSION['timeinterval'] = '%i, %H, %d, %j, %y';
}

if (!$_SESSION['sqltimeframe'] ){
$_SESSION['pledgemode'] = "amount";
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

		
<div id="ReloadThis">

<?php

//date_default_timezone_set(timezone_name_from_abbr("EST"));

			
	
$sql = "SELECT date, amount AS amount,
			SUM(differance) AS differance  FROM (SELECT * FROM pledges WHERE date > ".$_SESSION['sqltimeframe']." AND differance > -1 ORDER BY date DESC ) 
			pledges GROUP BY FROM_UNIXTIME(date, '".$_SESSION['timeinterval']."') ORDER BY date DESC";  //werkende control met tijdrestictie via settings.			
		
			$verbinding = new database ();
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
$mode = $_SESSION['pledgemode'];
	while($row = mysqli_fetch_array($test)){ 
		
	$overallpledged = $overallpledged + $row['differance'];
		
		
		  echo "['";
		  
		  switch ($_SESSION['timeinterval']) {
   case '%i, %H, %d, %j, %y':  // minutes
         	echo date("F j,H:i:s:T", $row['date']);
         break;
		  
	case '%H, %j, %y':  // hour
         	echo date("F j,H:i", $row['date']);
         break;
		 
	case '%j, %U, %y':  // day
         	echo date("F j", $row['date']);
         break;

	case '%U, %y':  // week
         	echo "Week ".date("W", $row['date']);
         break;
		 
	case '%m, %y': //month
         	echo date("F", $row['date']);
			
         break;	 }
		 
		 if ($i == ($aantalrows -1)){
		  
		  echo "', " . $row[$mode]."]"; } 
		  
		  else {
		  echo "', " . $row[$mode]."],"; 
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
		chartArea: {left:100, width: 350},
		colors: ['red','#004411','black'],
		hAxis: {
		direction: '-1',
		textPosition: 'none',
        textStyle: {color: '#000', fontName: 'Arial'}, 
        gridlines: { color: 'black', count: 5} 
      },vAxis: {
	  format : '$##,###,###', 
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
echo '<span></span><div id="any-height"><div id="main">';

echo '<div id="version">v0.8</div><div id="expand"><a href=""><img src="/images/expand.png" alt="expand" height="28" width="28"></a></div><h3>Star Citizen Pledge Counter</h3><br><div class="middle">';

if (!isset($_SESSION['history'])) {$_SESSION['history'] = $pieces[1];}
$nu = preg_replace( '/\D+/', '', $pieces[1] );
$toen = preg_replace( '/\D+/', '', $_SESSION['history'] );    
$verschil = $nu - $toen;
$som = 100 - ((10000000-$nu) / 100000);
echo "<center><h1>".$pieces[1]."</h1></center>";

if ($verschil == 0 ) {
   if ($verschil < 0 ) {$_SESSION['totaal'] = $_SESSION['totaal'] - $verschil;
		echo '<div class="information"><strong>Last change : $0 </strong><br>'; } else {
echo '<div class="information"><strong>Last change : $'. $_SESSION['verschil'].'</strong><br>';	
}
echo '<strong>Stretch goal : ';

if ( $som >= 100){ echo '<div class="green">reached !!</div></strong><br>';} else {echo number_format($som).'%</strong><br>'; }
echo '<strong>This session : $' . $_SESSION['totaal'].'</strong><br>';
}
		else {
$_SESSION['verschil'] = $verschil;
$_SESSION['totaal'] = $_SESSION['totaal'] + $verschil;

echo '<div class="information"><strong>Last change : $' . number_format($verschil).'.</strong><br>';
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
if ($_SESSION['tijdsduur'] == "604800"){echo "Week";}
if ($_SESSION['tijdsduur'] == "1209600"){echo "2 Weeks";}
if ($_SESSION['tijdsduur'] == "2419200"){echo "4 Weeks";}
echo '</h2></div>';

$_SESSION['history'] = $pieces[1];

 ?>

<center><div id="chart_div" style="width: 350px; height: 200px;"></div>


<div id="settingsTitle"><a href="">Settings</a></div>
<div id="closesettingsTitle"><a href="">X</a></div>

<script>

$(document).ready(function(){
 
        $("#settings").<?php if ($_SESSION['settings']){echo $_SESSION['settings'];} else {echo "hide";}?>();
		$("#closesettingsTitle").<?php if ($_SESSION['closesettingstitle']){echo $_SESSION['closesettingstitle'];} else {echo "hide";}?>();
		$("#settingsTitle").<?php if ($_SESSION['settingstitle']){echo $_SESSION['settingstitle'];} else {echo "show";}?>();
		$("#expand").<?php if ($_SESSION['expand']){echo $_SESSION['expand'];} else {echo "show";}?>();
		$("#retract").<?php if ($_SESSION['retract']){echo $_SESSION['retract'];} else {echo "hide";}?>();
		$("#aside").<?php if ($_SESSION['aside']){echo $_SESSION['aside'];} else {echo "hide";}?>();
		$("#any-height").css('width', '<?php if ($_SESSION['width']){echo $_SESSION['width'];} else {echo "450";}?>');
 
    $('#settingsTitle').click(function(){
    $("#settings").show();
	$("#closesettingsTitle").show();
	$("#settingsTitle").hide();
	$.post("passvar.php", { settings: "show" , settingstitle: "hide" , closesettingstitle: "show"});
	return false;
    });
	
	$('#closesettingsTitle').click(function(){
    $("#settings").hide();
	$("#settingsTitle").show();
	$("#closesettingsTitle").hide();
	$.post("passvar.php", { settings: "hide" , settingstitle: "show" , closesettingstitle: "hide"});
	return false;
    });
 
   $('#expand').click(function(){
		$("#expand").hide();
		$("#retract").show();
		$("#aside").show();
		$("#any-height").css('width', '800');
		$.post("passvar.php", { expand: "hide" , retract: "show" , aside: "show" , width: "800"});
		
	return false;
	
    });
	
	$('#retract').click(function(){
		$("#expand").show();
		$("#aside").hide();
		$("#retract").hide();
		$("#any-height").css('width', '450');
		$.post("passvar.php", {expand: "show" , retract: "hide" , aside : "hide" , width: "450"});

	return false;
    });
});


</script>

<div id="settings">
<form method="post" action="<?php echo $PHP_SELF;?>"> 
<fieldset>

<label>Pledge Mode :</label>
<select name="pledgemode">
<option value="amount" <?php if ($_SESSION['pledgemode'] == "amount"){echo "selected";}?>>Total Amount Pledged</option>
<option value="differance" <?php if ($_SESSION['pledgemode'] == "differance"){echo "selected";}?>>Individual Pledges</option></select><br>

<label>Refresh Rate :</label> 
<select name="refresh">
<option value="60" <?php if ($_SESSION['refresh'] == "60"){echo "selected";}?>>1 Minute</option>
<option value="120" <?php if ($_SESSION['refresh'] == "120"){echo "selected";}?>>2 Minutes</option>
<option value="180" <?php if ($_SESSION['refresh'] == "180"){echo "selected";}?>>3 Minutes</option>
<option value="300" <?php if ($_SESSION['refresh'] == "300"){echo "selected";}?>>5 Minutes</option>
<option value="600" <?php if ($_SESSION['refresh'] == "600"){echo "selected";}?>>10 Minutes</option>
<option value="never" <?php if ($_SESSION['refresh'] == "never"){echo "selected";}?>>Never</option></select><br>

<label>Chart Timeframe :</label>
<select name="timeframe">
<option value="3600" <?php if ($_SESSION['tijdsduur'] == "3600"){echo "selected";}?>>1 Hour</option>
<option value="21600" <?php if ($_SESSION['tijdsduur'] == "21600"){echo "selected";}?>>6 Hour</option>
<option value="43200" <?php if ($_SESSION['tijdsduur'] == "43200"){echo "selected";}?>>12 Hours</option>
<option value="86400" <?php if ($_SESSION['tijdsduur'] == "86400"){echo "selected";}?>>1 Day</option>
<option value="172800" <?php if ($_SESSION['tijdsduur'] == "172800"){echo "selected";}?>>2 Days</option>
<option value="604800" <?php if ($_SESSION['tijdsduur'] == "604800"){echo "selected";}?>>1 Week</option>
<option value="1209600" <?php if ($_SESSION['tijdsduur'] == "1209600"){echo "selected";}?>>2 Weeks</option>
<option value="2419200" <?php if ($_SESSION['tijdsduur'] == "2419200"){echo "selected";}?>>4 Weeks</option></select><br>

<label>Pledge Intervals :</label> 
<select name="timeinterval">
<option value="%i, %H, %d, %j, %y" <?php if ($_SESSION['timeinterval'] == "%i, %H, %d, %j, %y"){echo "selected";}?>>5 Minutes</option>
<option value="%H, %j, %y" <?php if ($_SESSION['timeinterval'] == "%H, %j, %y"){echo "selected";}?>>1 Hour</option>
<option value="%j, %U, %y" <?php if ($_SESSION['timeinterval'] == "%j, %U, %y"){echo "selected";}?>>1 Day</option>
<option value="%U, %y" <?php if ($_SESSION['timeinterval'] == "%U, %y"){echo "selected";}?>>1 Week</option>
<option value="%m, %y" <?php if ($_SESSION['timeinterval'] == "%m, %y"){echo "selected";}?>>1 Month</option></select><br>



 <input class="timeframe" name="submit" value="submit" type="submit">

 </fieldset>
</form><br></center>


<?php 
echo '</div><div class="time">Updated ' . $tijdsduur .' '.date("H:i:s , T") . '</div></div>'; 

unset ($pieces);
$html->clear();
clearstatcache();
?>

<div id="aside">
<div id="retract"><a href=""><img src="/images/retract.png" alt="expand" height="28" width="28"></a></div>
<?php

echo "<center><h2>pledges by ";

switch ($_SESSION['timeinterval']) {
   case '%i, %H, %d, %j, %y':  // minutes
         	echo "minutes";
         break;
		  
	case '%H, %j, %y':  // hour
         	echo "hour";
         break;
		 
	case '%j, %U, %y':  // day
         	echo "day";
         break;

	case '%U, %y':  // week
         	echo "week ";
         break;
		 
	case '%m, %y': //month
         	echo "month";
         break;	 }

echo "</h2><br></center>";

$verbinding2 = new database ();
$test2 = $verbinding2->queryDb ( $sql );

while($row2 = mysqli_fetch_array($test2)){ 

  
switch ($_SESSION['timeinterval']) {
   case '%i, %H, %d, %j, %y':  // minutes
         	echo date("F j, H:i", $row2['date'])." : $ <strong>" . number_format($row2[$mode]).'</strong><br>';
         break;
		  
	case '%H, %j, %y':  // hour
         	echo date("F j, H:i", $row2['date'])." : $ <strong>" . number_format($row2[$mode]).'</strong><br>';
         break;
		 
	case '%j, %U, %y':  // day
         	echo date("F j", $row2['date'])." : $ <strong>" . number_format($row2[$mode]).'</strong><br>';
         break;

	case '%U, %y':  // week
         	echo "Week ".date("W", $row2['date'])." : $ <strong>" . number_format($row2[$mode]).'</strong><br>';
         break;
		 
	case '%m, %y': //month
         	echo date("F", $row2['date'])." : $ <strong>" . number_format($row2[$mode]).'</strong><br>';
         break;	 }
  
  
 } 
?><br></div></div>


<style></style>
<footer>brought to you by Zeroiser</footer>
</body>
</html>