<?php

$sourceFolder = "sourcedata/";
//$outputFolder = "importdata/";







//require_once './lib/PHPExcel/PHPExcel/IOFactory.php';

// =========================================================
// Wanderungsdaten
// =========================================================

echo "\nUeberpruefe Wanderungsdaten.\n";

ini_set('auto_detect_line_endings',TRUE);

$relations = array();

$csvDateien = glob($sourceFolder."*.DBF.csv");
foreach ($csvDateien as $dateiname) {
	echo "\n Lese ".$dateiname."\n";
	$csvFile = fopen( $dateiname,'r');

	$header = fgetcsv( $csvFile );
	if(is_numeric($header[0])) {
		echo "\n Header ist nicht vorhanden!\n Datei beginnt mit Zahlenwert: ".$header[0]."\n";
    }
	$linecount = 0;
	// Liest Zeile für Zeile aus der Datei aus
	while (($line = fgetcsv($csvFile)) !== FALSE) {
		$linecount++;
		if ($line[0] < 1990 || $line[0] > 2011){
		echo ("\n   Zeile:".($linecount)." hat falsche Datumsangabe");
		}
		if ((strlen($line[1])) < 9 ||(strlen($line[1])) > 9){
		echo ("\n   Zeile:".($linecount)." hat falschen AGS-Wert");
		}
		if ($line[2] > 1){
		echo ("\n   Zeile:".($linecount)." hat falschen Wert in Feld Umzugsart");
		}
		if ((strlen($line[3])) < 9 ||(strlen($line[3])) > 9){
		echo ("\n   Zeile:".($linecount)." hat falschen AGS-2-Wert");
		}
		if ($line[4] > 2 || $line[4] < 1){
		echo ("\n   Zeile:".($linecount)." hat falschen Wert in Feld Geschlecht");
		}
		if ($line[5] < 1){
		echo ("\n   Zeile:".($linecount)." hat falschen Wert in Feld Anzahl");
		}
		if ($line[6] > 7 || $line[6] < 1){
		echo ("\n   Zeile:".($linecount)." hat falschen Wert in Feld Altersgruppe");
		}
		if ($line[7] > 2){
		echo ("\n   Zeile:".($linecount)." hat falschen Wert in Feld Familienstand");
		}
		if (!$line[8]== "D" || !$line[8] == "A"){
		echo ("\n   Zeile:".($linecount)." hat falschen Nation-Wert");
		}
	}
fclose($csvFile);
}

?>