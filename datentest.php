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

$csvDateien = glob($sourceFolder."*.DBF.csv");
foreach ($csvDateien as $dateiname) {
	echo "\n \n Lese ".$dateiname."\n";
	$csvFile = fopen( $dateiname,'r');
	$falschname = "test";
	$header = fgetcsv( $csvFile );
	
	$sperk = spaltenerkennung($header);
	$linecount = 1;
	/*if(is_numeric($header[0])) {
		echo "\n Header ist nicht vorhanden!\n Datei beginnt mit Zahlenwert: ".$header[0]."\n";
    }
	if (((substr( $header[0], 0, 4 )) === "JAHR") && ((substr( $header[1], 0, 4 )) === "SCHL") && ((substr( $header[2], 0, 3 )) === "ART") && ((substr( $header[3], 0, 5 )) === "SCHL1") && ((substr( $header[4], 0, 6 )) === "GESCHL") && ((substr( $header[5], 0, 3 )) === "ANZ") && ((substr( $header[6], 0, 2 )) === "AG") && ((substr( $header[7], 0, 3 )) === "FAM") && ((substr( $header[8], 0, 3 )) === "NAT")){
		echo " Hier sind die Spalten korrekt angeordnet: ".$dateiname."\n";
	}else{
		echo " ACHTUNG andere Spaltenanordnung! Datei wird vorerst ignoriert: ".$dateiname."\n";
		echo $header[0]." | ".$header[1]." | ".$header[2]." | ".$header[3]." | ".$header[4]." | ".$header[5]." | ".$header[6]." | ".$header[7]." | ".$header[8];
	}
		$linecount = 1;
		// Liest Zeile für Zeile aus der Datei aus
	if ($klaus===null) {
		die("Ich kann so nicht arbeiten....");
	}*/
	while ((($line = fgetcsv($csvFile)) !== FALSE) /*&& ((substr( $header[0], 0, 4 )) === "JAHR") && ((substr( $header[1], 0, 4 )) === "SCHL") && ((substr( $header[2], 0, 3 )) === "ART") && ((substr( $header[3], 0, 5 )) === "SCHL1") && ((substr( $header[4], 0, 6 )) === "GESCHL") && ((substr( $header[5], 0, 3 )) === "ANZ") && ((substr( $header[6], 0, 2 )) === "AG") && ((substr( $header[7], 0, 3 )) === "FAM") && ((substr( $header[8], 0, 3 )) === "NAT")*/){
	 
		$linecount++;
		if ($line[$sperk['JAHR']] < 1990 || $line[$sperk['JAHR']] > 2011){
		echo ("\n   Zeile:".($linecount)." hat falsche Datumsangabe: ".$line[$sperk['JAHR']]);
		}
		if ((strlen($line[$sperk['SCHL']])) < 9 ||(strlen($line[$sperk['SCHL']])) > 9){
		echo ("\n   Zeile:".($linecount)." hat falschen AGS-Wert: ".$line[$sperk['SCHL']]);
		}
		if ($line[$sperk['ART']] > 1){
		echo ("\n   Zeile:".($linecount)." hat falschen Wert in Feld Umzugsart: ".$line[$sperk['ART']]);
		}
		if ((strlen($line[$sperk['SCHL1']])) < 9 ||(strlen($line[$sperk['SCHL']])) > 9){
		echo ("\n   Zeile:".($linecount)." hat falschen AGS-2-Wert: ".$line[$sperk['SCHL1']]);
		}
		if ($line[$sperk['GESCHL']] > 2 || $line[$sperk['GESCHL']] < 1){
		echo ("\n   Zeile:".($linecount)." hat falschen Wert in Feld Geschlecht: ".$line[$sperk['GESCHL']]);
		}
		if ($line[$sperk['ANZ']] < 1){
		echo ("\n   Zeile:".($linecount)." hat falschen Wert in Feld Anzahl: ".$line[$sperk['ANZ']]);
		}
		if ($line[$sperk['AG']] > 7 || $line[$sperk['AG']]  < 1){
		echo ("\n   Zeile:".($linecount)." hat falschen Wert in Feld Altersgruppe: ".$line[$sperk['AG']]);
		}
		if ($line[$sperk['FAM']]  > 2){
		echo ("\n   Zeile:".($linecount)." hat falschen Wert in Feld Familienstand: ".$line[$sperk['FAM']]);
		}
		if (!$line[$sperk['NAT']] == "D" || !$line[$sperk['NAT']] == "A"){
		echo ("\n   Zeile:".($linecount)." hat falschen Nation-Wert: ".$line[$sperk['NAT']]);
		}
	
	}
	
	fclose($csvFile);
	
	
}
?>