<?php

$sourceFolder = "sourcedata/wander/";
//$outputFolder = "importdata/";


include "wanderungsdaten.php";




//require_once './lib/PHPExcel/PHPExcel/IOFactory.php';

// =========================================================
// Wanderungsdaten
// =========================================================

echo "\nUeberpruefe Wanderungsdaten.\n";

ini_set('auto_detect_line_endings',TRUE);

$start_zeit = microtime(true);

$relations = array();
$totallinecount=0;
$totalcount=0;
$csvDateien = glob($sourceFolder."*.DBF.csv");
foreach ($csvDateien as $dateiname) {
	echo "\n \n Lese ".$dateiname."\n";
	$csvFile = fopen( $dateiname,'r');
	$falschname = "test";
	$header = fgetcsv( $csvFile );
	
	$sperk = spaltenerkennung($header);
	$linecount = 1;
	$count = 0;
	
	$jahr = $sperk['JAHR'];
	$schl = $sperk['SCHL'];
	$art = $sperk['ART'];
	$schl1 = $sperk['SCHL1'];
	$geschl = $sperk['GESCHL'];
	$anzahl = $sperk['ANZ'];
	$altersgruppe = $sperk['AG'];
	$familienstand = $sperk['FAM'];
	$nation = $sperk['NAT'];
	
	while ((($line = fgetcsv($csvFile)) !== FALSE)){
		
		$linecount++;
		if ($line[$jahr] < 1990 || $line[$jahr] > 2011){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falsche Datumsangabe: ".$line[$jahr]);
		}
		if ((strlen($line[$schl])) < 9 ||(strlen($line[$schl])) > 9){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falschen AGS-Wert: ".$line[$schl]);
		}
		if ($line[$art] > 1){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falschen Wert in Feld Umzugsart: ".$line[$art]);
		}
		if ((strlen($line[$schl1])) < 9 ||(strlen($line[$schl1])) > 9){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falschen AGS-2-Wert: ".$line[$schl1]);
		}
		if ($line[$geschl] > 2 || $line[$geschl] < 1){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falschen Wert in Feld Geschlecht: ".$line[$geschl]);
		}
		if ($line[$anzahl] < 1){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falschen Wert in Feld Anzahl: ".$line[$anzahl]);
		}
		if ($line[$altersgruppe] > 7 || $line[$altersgruppe]  < 1){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falschen Wert in Feld Altersgruppe: ".$line[$altersgruppe]);
		}
		if ($line[$familienstand]  > 2){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falschen Wert in Feld Familienstand: ".$line[$familienstand]);
		}
		if (!$line[$nation] == "D" || !$line[$nation] == "A"){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falschen Nation-Wert: ".$line[$nation]);
		}

	}
	$totallinecount+=$linecount;
	$totalcount+=$count;
	$fehlerquote = $totalcount/$totallinecount*100;
	echo "\n Fehleranzahl: ".$totalcount." von ".$totallinecount;
	echo "\n Fehlerquote: ".$fehlerquote;
	fclose($csvFile);
	
	
}

echo "Zeit: ".(microtime(true)-$start_zeit)."s ";
?>