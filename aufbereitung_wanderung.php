<?php
// =========================================================
// Aufbereitung Wanderungsdaten
// =========================================================

/* Folgende Variablen stehen im Kontext zur Verfügung

// Letzte erzeugte nodeID
$nodeID = 0;

// NodeID Fläche
$flID = 0;

// NodeID Bevölkerung
$bevID = 0;

// NodeIDs für Räume
// array( "DE" => 5, "01" => 24, "01001"=> 22321 );
$spaceIDs = array();

// NodeIDs für Datum, array( "20121231"=>45 );
$dateIDs = array();

*/

echo "\nVerarbeite Wanderungsdaten.\n";

ini_set('auto_detect_line_endings',TRUE);

$relations = array();

$csvDateien = glob($sourceFolder."*.DBF.csv");
foreach ($csvDateien as $dateiname) {
	echo "\n  Lese ".$dateiname;
	$csvFile = fopen( $dateiname,'r');
	
	$header = fgetcsv( $csvFile );
	// Liest Zeile für Zeile aus der Datei aus
	while (($line = fgetcsv($csvFile)) !== FALSE) {
		//echo (count($line)." ".$line[0]." - ".$line[1]."\n");
		$line[0] = $line[0].'1231';
		if (substr($line[3],0,1) == '0') {
			$line[3] = substr($line[3],1,8);
		}else{
			$line[3] = substr($line[3],0,3);
		}	
		if (substr($line[1],0,1) == '0') {
			$line[1] = substr($line[1],1,8);
		}else{
			$line[1] = substr($line[1],0,3);
		}	
		// echo (count($line)." - ".$line[0]." -  ".$line[1]." - ".$line[3]."\n");

		// Knoten-ID für AGS aus $spaceIDs suchen
		
		// Prüfen, ob für die AGS auch für dieses Jahr existiert?
		
		// Wanderungsdaten als Relationen speichern
		//$relations[] = array($start, $end, $type, null, null, 0);
	}
	fclose($csvFile);
}

// Öffne Relations-Ausgabedatei erneut, jetzt aber zum anhängen

$relFile = fopen($outputFolder.'rels.csv','a');
foreach( $relations as $rel) {
	fwrite( $relFile, "\n".implode("\t", $rel));
}
// Schließe Ausgabedatei
fclose($relFile);

?>