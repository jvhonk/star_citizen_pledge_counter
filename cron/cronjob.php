<?php

	require_once 'database.php';
	require_once 'simple_html_dom.php';
	

function pull($url){
$output = file_get_html($url)->find("div", 15);
return $output;
}

$html = pull("http://www.robertsspaceindustries.com");
$pieces = explode("<strong>", $html->innertext);

$opvragen = new database ();
			$opvraagsql = "SELECT amount FROM pledges ORDER BY date DESC LIMIT 0, 1;";
			$opvraag = $opvragen->queryDb ( $opvraagsql );
    $vorige = mysqli_fetch_array($opvraag);
	
	$verbinding = new database ();
    $tijd = time();
	$amount = preg_replace( '/\D+/', '', $pieces[1]);
echo $amount."<br>";	
    $verschil = $amount - $vorige['amount'];
	$sql= "INSERT INTO pledges (date, amount, differance) VALUES ('$tijd','$amount', '$verschil')";
				$verbinding->insertDb ( $sql );
		
    $html->clear();				
	
				?>
				
			