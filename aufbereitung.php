<?php

$sourceFolder = "sourcedata/";
$outputFolder = "importdata/";







require_once './lib/PHPExcel/PHPExcel/IOFactory.php';
require_once( './sourcedata/formate.php');
require("phar://lib/neo4jphp.phar");
//require_once('./lib/XLS.php');

use Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Cypher;
	

// INIT Neo4j Client
$neoClient = new Client();

// INIT Excel-Reader
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

// INIT AUFBEREITUNG

// Letzte erzeugte nodeID
$nodeID = 0;
// NodeID Fläche
$flID = 0;
// NodeID Bevölkerung
$bevID = 0;
// NodeIDs für Räume
// array( "DE" => 5, "01" => 24, "01001"=> 22321 );
$spaceIDs = array();

// Knoten und Relationen
$nodes = array();
$relations = array();

// Verpflichtende Knotenspalten
//					0				1
$nodeProps = array("ags:string",	"name:string");
// Verpflichtende Relationenspalten
//					0		1		2		3			4				5
$relProps = array("start",	"end",	"type",	"wert:float",	"teil:float",	"t:int");

$relTypeAdmin = "TEIL";
$relTypeFl = "FL";
$relTypeBev = "BEV";







// Test, ob Datenbank leer ist
$query = new Cypher\Query($neoClient, "START n=node(*) RETURN max(ID(n)) AS maxNodeId");
$result = $query->getResultSet();
$maxNodeID = $result[0]["maxNodeId"];
if ($maxNodeID>0) {
	// Abbruch des Skripts
	die("Die Datenbank ist nicht leer! Ist im Moment nicht OK.");
} else {
	echo "Datenbank ist leer. Ist OK.";
}


// Hauptschleife durch XLS-Dateien
foreach($dateiformate as $fileName=>$sheets) {
	$xlsReader = PHPExcel_IOFactory::createReader('Excel5');
	$xlsReader->setReadDataOnly(true);
	//if (!$xlsReader->canRead($sourceFolder.$fileName)) die( "Datei $sourceFolder$fileName nicht lesbar. Excel 5 wird benötigt.");
	
	// Schleife durch Tabellen
	foreach($sheets as $sheetName=>$sheetInfo) {
		
		
		echo "\n  Lese $fileName > $sheetName... ";
		$xlsReader->setLoadSheetsOnly($sheetName);
		$xls = $xlsReader->load( $sourceFolder.$fileName );
		$sheet = $xls->getSheetByName($sheetName);
		// $xls->getSheetByName($sheetName);
		//$xls = new XLS($sourceFolder.$fileName, $sheetName);
		echo "OK. Verarbeite...";
		
		switch ($sheetInfo[1]) {
			case "A": 
				// col	0		1			2			3			4			5
				//		AGS		Gemeinde	Fläche km2	Bevölkerung		
				//										insgesamt	männlich	weiblich
				$row = 5;
				
				$val = $sheet->getCellByColumnAndRow(0, $row)->getValue();
				while (!empty($val)){
					
					
					$land = substr($val,0,2);
					$landNodeID = @$spaceIDs[$land];
					if (!$landNodeID) {
						// Land existiert noch nicht und muss angelegt werden
						$nodeID++;
						$nodes[] = array($land, $land);
						$landNodeID = $spaceIDs[$land] = $nodeID;
						
						// Relation zu DE anlegen
						$relations[] = array($spaceIDs['DE'], $landNodeID, $relTypeAdmin, null, null, $sheetInfo[0]);
					}
					
					$kreis = substr($val,0,5);
					$kreisNodeID = @$spaceIDs[$kreis];
					if (!$kreisNodeID) {
						// Kreis existiert noch nicht und muss angelegt werden
						$nodeID++;
						$nodes[] = array($kreis, $kreis);
						$kreisNodeID = $nodeID;
						$spaceIDs[$kreis] = $nodeID;
						
						// Relation zum Land anlegen
						$relations[] = array($landNodeID, $kreisNodeID, $relTypeAdmin, null, null, $sheetInfo[0]);
					}
					
					$ags = $val;
					$name = $sheet->getCellByColumnAndRow(1, $row)->getValue();
					
					$gemeindeNodeID = @$spaceIDs[$ags];
					if (!$gemeindeNodeID) {
						// Gemeinde existiert noch nicht und muss angelegt werden
						$nodeID++;
						$nodes[] = array($ags, $name);
						$gemeindeNodeID = $nodeID;
						$spaceIDs[$ags] = $nodeID;
					}
					
					// Relation zum Kreis anlegen
					$relations[] = array($kreisNodeID, $gemeindeNodeID, $relTypeAdmin, null, null, $sheetInfo[0]);
					
					// Relation zu Daten anlegen
					$relations[] = array($gemeindeNodeID, $flID,
								$relTypeFl, $sheet->getCellByColumnAndRow(2, $row)->getValue(),
								null, $sheetInfo[0]);
					$relations[] = array($gemeindeNodeID, $bevID,
								$relTypeBev, $sheet->getCellByColumnAndRow(3, $row)->getValue(),
								null, $sheetInfo[0]);
					
					$val = $sheet->getCellByColumnAndRow(0, ++$row)->getValue();
				}
				
			break; // A ================================================================
			case "B": 
				//	0		1		2		3	4		5	6	7				8		9			10			11			12		13
				//	Satzart	Textk.	Regionalschlüssel (RS)		Gemeindename	Fläche	Bevölkerung	-			-			-		PLZ
				//					Land	RB	Kreis	VB	Gem							insgesamt	männlich	weiblich	je km2	
				$row = 7;
			
			break; // B ================================================================
			case "NODES":
				// col	0		1			2
				//		id		ags:string	name:string

				$col = 0;
				$row = 2;
				$val = $sheet->getCellByColumnAndRow(0, $row)->getValue();
				while (!empty($val)){
					$ags=$sheet->getCellByColumnAndRow(1, $row)->getValue();
					$name=$sheet->getCellByColumnAndRow(2, $row)->getValue();
					
					$nodeID++;
					$nodes[] = array($ags, $name);
					
					// Bestimmte Node-IDs zur späteren Verwendung speichern
					if ($name=="Fläche") {
						$flID = $nodeID;
					} else if ($name=="Bevölkerung") {
						$bevID = $nodeID;
					} else if ($ags=="DE") {
						$spaceIDs['DE'] = $nodeID;
					}
					
					$val = $sheet->getCellByColumnAndRow(0, ++$row)->getValue();
				}
			break; // NODES ============================================================
			case "RELS":
				// col	0		1		2		3
				// 		start	end		type	t:int

				$col = 0;
				$row = 2;
				
				$val = $sheet->getCellByColumnAndRow(0, $row)->getValue();
				while ($val!==null){
					$start = $val;
					$end = $sheet->getCellByColumnAndRow(1, $row)->getValue();
					$type = $sheet->getCellByColumnAndRow(2, $row)->getValue();
					
					$relations[] = array($start, $end, $type, null, null, 0);
					
					$val = $sheet->getCellByColumnAndRow(0, ++$row)->getValue();
				}
			
			break; // RELS =============================================================
		}
		
		// Aufräumen
		$xls->disconnectWorksheets();
		unset( $sheet );
		unset( $xls );
		echo "OK.";
	}
	unset( $xlsReader );
}

// Öffne Ausgabedatei
$nodeFile = fopen($outputFolder.'nodes.csv','w');
fwrite( $nodeFile, implode("\t", $nodeProps));
foreach( $nodes as $node) {
	fwrite( $nodeFile, "\n".implode("\t", $node));
}
// Schließe Ausgabedateien
fclose($nodeFile);


// Öffne Ausgabedatei
$relFile = fopen($outputFolder.'rels.csv','w');
fwrite( $relFile, implode("\t", $relProps));
foreach( $relations as $rel) {
	fwrite( $relFile, "\n".implode("\t", $rel));
}
// Schließe Ausgabedatei
fclose($relFile);


?>