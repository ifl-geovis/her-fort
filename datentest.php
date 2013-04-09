<?php

$sourceFolder = "sourcedata/wander/";
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
	echo "\n \n Lese ".$dateiname."\n";
	$csvFile = fopen( $dateiname,'r');
	$falschname = "test";
	$header = fgetcsv( $csvFile );
	if(is_numeric($header[0])) {
		echo "\n Header ist nicht vorhanden!\n Datei beginnt mit Zahlenwert: ".$header[0]."\n";
    }
	if (((substr( $header[0], 0, 4 )) === "JAHR") && ((substr( $header[1], 0, 4 )) === "SCHL") && ((substr( $header[2], 0, 3 )) === "ART") && ((substr( $header[3], 0, 5 )) === "SCHL1") && ((substr( $header[4], 0, 6 )) === "GESCHL") && ((substr( $header[5], 0, 3 )) === "ANZ") && ((substr( $header[6], 0, 2 )) === "AG") && ((substr( $header[7], 0, 3 )) === "FAM") && ((substr( $header[8], 0, 3 )) === "NAT")){
		echo " Hier sind die Spalten korrekt angeordnet: ".$dateiname."\n";
	}
		$linecount = 1;
		// Liest Zeile für Zeile aus der Datei aus
		//while (( bedingung1 ) && (bedigung2) ) {
		//substr( $string_n, 0, 4 ) === "http"
	while ((($line = fgetcsv($csvFile)) !== FALSE) && ((substr( $header[0], 0, 4 )) === "JAHR") && ((substr( $header[1], 0, 4 )) === "SCHL") && ((substr( $header[2], 0, 3 )) === "ART") && ((substr( $header[3], 0, 5 )) === "SCHL1") && ((substr( $header[4], 0, 6 )) === "GESCHL") && ((substr( $header[5], 0, 3 )) === "ANZ") && ((substr( $header[6], 0, 2 )) === "AG") && ((substr( $header[7], 0, 3 )) === "FAM") && ((substr( $header[8], 0, 3 )) === "NAT")){
	 
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