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
	
	while ((($line = fgetcsv($csvFile)) !== FALSE)){
		
		$linecount++;
		if ($line[$sperk['JAHR']] < 1990 || $line[$sperk['JAHR']] > 2011){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falsche Datumsangabe: ".$line[$sperk['JAHR']]);
		}
		if ((strlen($line[$sperk['SCHL']])) < 9 ||(strlen($line[$sperk['SCHL']])) > 9){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falschen AGS-Wert: ".$line[$sperk['SCHL']]);
		}
		if ($line[$sperk['ART']] > 1){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falschen Wert in Feld Umzugsart: ".$line[$sperk['ART']]);
		}
		if ((strlen($line[$sperk['SCHL1']])) < 9 ||(strlen($line[$sperk['SCHL']])) > 9){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falschen AGS-2-Wert: ".$line[$sperk['SCHL1']]);
		}
		if ($line[$sperk['GESCHL']] > 2 || $line[$sperk['GESCHL']] < 1){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falschen Wert in Feld Geschlecht: ".$line[$sperk['GESCHL']]);
		}
		if ($line[$sperk['ANZ']] < 1){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falschen Wert in Feld Anzahl: ".$line[$sperk['ANZ']]);
		}
		if ($line[$sperk['AG']] > 7 || $line[$sperk['AG']]  < 1){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falschen Wert in Feld Altersgruppe: ".$line[$sperk['AG']]);
		}
		if ($line[$sperk['FAM']]  > 2){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falschen Wert in Feld Familienstand: ".$line[$sperk['FAM']]);
		}
		if (!$line[$sperk['NAT']] == "D" || !$line[$sperk['NAT']] == "A"){
			$count++;
			echo ("\n Fehler-Nr.: ".$count."| Zeile:".($linecount)." hat falschen Nation-Wert: ".$line[$sperk['NAT']]);
		}

	}
	$totallinecount+=$linecount;
	$totalcount+=$count;
	$fehlerquote = $totalcount/$totallinecount*100;
	echo "\n Fehleranzahl: ".$totalcount." von ".$totallinecount;
	echo "\n Fehlerquote: ".$fehlerquote;
	fclose($csvFile);
	
	
}
?>