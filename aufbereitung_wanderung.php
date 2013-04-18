<?php
// =========================================================
// Aufbereitung Wanderungsdaten
// =========================================================

/* Folgende Variablen stehen im Kontext zur Verf�gung

// Letzte erzeugte nodeID
$nodeID = 0;

// NodeID Fl�che
$flID = 0;

// NodeID Bev�lkerung
$bevID = 0;

// NodeIDs f�r R�ume
// array( "DE" => 5, "01" => 24, "01001"=> 22321 );
$spaceIDs = array();

// NodeIDs f�r Datum, array( "20121231"=>45 );
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
	// Liest Zeile f�r Zeile aus der Datei aus
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

		// Knoten-ID f�r AGS aus $spaceIDs suchen
		
		// Pr�fen, ob f�r die AGS auch f�r dieses Jahr existiert?
		
		// Wanderungsdaten als Relationen speichern
		//$relations[] = array($start, $end, $type, null, null, 0);
	}
	fclose($csvFile);
}

// �ffne Relations-Ausgabedatei erneut, jetzt aber zum anh�ngen

$relFile = fopen($outputFolder.'rels.csv','a');
foreach( $relations as $rel) {
	fwrite( $relFile, "\n".implode("\t", $rel));
}
// Schlie�e Ausgabedatei
fclose($relFile);

?>