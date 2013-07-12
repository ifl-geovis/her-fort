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
$sourceFolder = "sourcedata/wander/";//zum Testen eingefügt

include "wanderungsdaten.php";

echo "\nVerarbeite Wanderungsdaten.\n";

ini_set('auto_detect_line_endings',TRUE);

$relations = array();

$counts = array();
$imLandCount = array();


$csvDateien = glob($sourceFolder."*.DBF.csv");
foreach ($csvDateien as $dateiname) {
	echo "\n Lese ".$dateiname;
	$csvFile = fopen( $dateiname,'r');
	
	$index = array();
	$doppel = array();
	$header = fgetcsv( $csvFile );
	$sperk = spaltenerkennung($header);
	
	$jahr = $sperk['JAHR'];
	$schl = $sperk['SCHL'];
	$art = $sperk['ART'];
	$schl1 = $sperk['SCHL1'];
	$geschl = $sperk['GESCHL'];
	$anzahl = $sperk['ANZ'];
	$altersgruppe = $sperk['AG'];
	$familienstand = $sperk['FAM'];
	$nation = $sperk['NAT'];
	
	$zuzugsum = array();
	$fortzugsum = array();
	$zuzuginner= array();
	$fortzuginner= array();
	
	// Liest Zeile für Zeile aus der Datei aus
	while (($line = fgetcsv($csvFile)) !== FALSE) {
		//echo (count($line)." ".$line[0]." - ".$line[1]."\n");
		
		//Abfrage der Gesamtanzahl Wanderungen
		if ($line[$art] == 0){
			$zuzugsum[] += $line[$anzahl];
		}else{
			$fortzugsum[] += $line[$anzahl];
		}
		
		$line[$jahr] = $line[$jahr].'1231';
		if (substr($line[$schl1],0,1) == '0') {
			$line[$schl1] = substr($line[$schl1],1,8);
		}else{
			$line[$schl1] = substr($line[$schl1],0,3);
		}	
		if (substr($line[$schl],0,1) == '0') {
			$line[$schl] = substr($line[$schl],1,8);
		}else{
			$line[$schl] = substr($line[$schl],0,3);
		}	
		// echo (count($line)." - ".$line[0]." -  ".$line[1]." - ".$line[3]."\n");
		$schluessel = $line[$schl].$line[$schl1].$line[$art].$line[$geschl].$line[$altersgruppe].$line[$familienstand].$line[$nation];
		$anzahlwert = $line[$anzahl];
		$wert = 1;
		//echo $schluessel."\n";
		if (isset($index[$schluessel])) {
			//echo "   Doppelter Wert: ".$schluessel."\n";
			$index[$schluessel] += $anzahlwert;
			@$counts[$line[$schl1]]++;
		} else {
			$index[$schluessel] = $anzahlwert;
		}
		//$key = 0;
		if (substr($line[$schl],0,2)== substr($line[$schl1],0,2)) {
			if ($line[$art] == 0) {
				$key = $line[$schl].$line[$schl1].$line[$geschl].$line[$altersgruppe].$line[$familienstand].$line[$nation].$line[$anzahl];
				$zuzuginner[] += $line[$anzahl];
			} else {
				$key = $line[$schl1].$line[$schl].$line[$geschl].$line[$altersgruppe].$line[$familienstand].$line[$nation].$line[$anzahl];
				$fortzuginner[] += $line[$anzahl];
			}
			
			if (isset($doppel[$key])){
				//echo "   Doppelt aufgenommen: ".$key."\n";
				$doppel[$key] += $wert;
				@$imLandCount[$line[$schl1]]++;
			} else {
				$doppel[$key] = $wert;
			}
		}
		
		// Knoten-ID für AGS aus $spaceIDs suchen
		
		// Prüfen, ob für die AGS auch für dieses Jahr existiert?
		
		// Wanderungsdaten als Relationen speichern
		//$relations[] = array($start, $end, $type, null, null, 0);
		//print_r ($index);
		
	}
	fclose($csvFile);
	//echo " Zuzug: ".array_sum($zuzugsum).", Fortzug: ".array_sum($fortzugsum);
	echo " Zuzug: ".array_sum($zuzuginner).", Fortzug: ".array_sum($fortzuginner);
	print_r($doppel);
}

$doppelcount = array();
foreach($imLandCount as $key=>$value) {
	$doppelcount[$key] = $value - $counts[$key]; 
}
$gesamt = array_sum($doppelcount);
print_r($doppelcount);
print_r($gesamt);

//print_r($counts);
// Öffne Relations-Ausgabedatei erneut, jetzt aber zum anhängen
/*//zum Testen auskommentiert
$relFile = fopen($outputFolder.'rels.csv','a');
foreach( $relations as $rel) {
	fwrite( $relFile, "\n".implode("\t", $rel));
}
// Schließe Ausgabedatei
fclose($relFile);
*/
?>