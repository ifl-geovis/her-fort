<?php

$sourceFolder = "sourcedata/";
$outputFolder = "importdata/";







require_once './lib/PHPExcel/PHPExcel/IOFactory.php';
require_once( './sourcedata/formate.php');
require("phar://lib/neo4jphp.phar");
//require_once('./lib/XLS.php');

/*
// Zur Zeit deaktiviert
use Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Cypher;
	

// INIT Neo4j Client
$neoClient = new Client();
*/

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
// NodeIDs für Datum, array( "20121231"=>45 );
$dateIDs = array();

$geschlechtIDs = array();
$familieIDs = array();
$alterIDs = array();
$wanderungIDs = array();
$nationIDs = array();

// Knoten und Relationen
$nodes = array();
$relations = array();

// Verpflichtende Knotenspalten
//					0				1					2
$nodeProps = array("ags:string",	"klasse:string",	"name:string");
// Verpflichtende Relationenspalten
//					0		1		2		3			4				5
$relProps = array("start",	"end",	"type",	"wert:float",	"teil:float",	"d:int");

$relTypeAdmin = "TEIL";
$relTypeFl = "FL";
$relTypeBev = "BEV";


// Test, ob Datenbank leer ist
if (isset($neoClient)) {
	$query = new Cypher\Query($neoClient, "START n=node(*) RETURN max(ID(n)) AS maxNodeId");
	$result = $query->getResultSet();
	$maxNodeID = $result[0]["maxNodeId"];
	if ($maxNodeID>0) {
		// Abbruch des Skripts
		die("Die Datenbank ist nicht leer! Ist im Moment nicht OK.");
	} else {
		echo "Datenbank ist leer. Ist OK.";
	}
}

// Schreibe Ausgabedatei-Header
$nodeFile = fopen($outputFolder.'nodes.csv','w');
fwrite( $nodeFile, implode("\t", $nodeProps));
fclose($nodeFile);
$relFile = fopen($outputFolder.'rels.csv','w');
fwrite( $relFile, implode("\t", $relProps));
fclose($relFile);

// =========================================================
// Gemeindedaten
// =========================================================

// Hauptschleife durch XLS-Dateien
foreach($dateiformate as $fileName=>$sheets) {
	$xlsReader = PHPExcel_IOFactory::createReader('Excel5');
	$xlsReader->setReadDataOnly(true);
	//if (!$xlsReader->canRead($sourceFolder.$fileName)) die( "Datei $sourceFolder$fileName nicht lesbar. Excel 5 wird benötigt.");

	// Schleife durch Tabellen
	foreach($sheets as $sheetName=>$sheetInfo) {
		// Knoten und Relationen
		$nodes = array();
		$relations = array();
		
		echo "\n  Lese $fileName > $sheetName... ";
		$xlsReader->setLoadSheetsOnly($sheetName);
		$xls = $xlsReader->load( $sourceFolder.$fileName );
		$sheet = $xls->getSheetByName($sheetName);
		// $xls->getSheetByName($sheetName);
		//$xls = new XLS($sourceFolder.$fileName, $sheetName);
		echo "OK. Verarbeite...";
		
		
		
		switch ($sheetInfo[1]) {
			// NODES und RELS müssen zuerst abgearbeitet werden, d.h. in der formate.php ganz am Anfang stehen
			case "NODES":
				// col	0		1			2				3
				//		id		ags:string	klasse:string	name:string

				$col = 0;
				$row = 2;
				$val = $sheet->getCellByColumnAndRow(0, $row)->getValue();
				while (!empty($val)){
					$ags=$sheet->getCellByColumnAndRow(1, $row)->getValue();
					$klasse=$sheet->getCellByColumnAndRow(2, $row)->getValue();
					$name=$sheet->getCellByColumnAndRow(3, $row)->getValue();
					
					$nodeID++;
					
					// Bestimmte Node-IDs zur späteren Verwendung speichern
					if ($name=="Fläche") {
						$flID = $nodeID;
					} else if ($name=="Bevölkerung") {
						$bevID = $nodeID;
					} else if ($ags=="DE") {
						$spaceIDs['DE'] = $nodeID;
					} else if ($name=="Datum") {
						$dateIDs['node'] = $nodeID;
					}
					
					switch ($ags) {
						case "Geschlecht":
							$geschlechtIDs[$klasse] = $nodeID;
							$ags = null;
						break;
						case "Familienstand":
							$familieIDs[$klasse] = $nodeID;
							$ags = null;
						break;
						case "Altersgruppe":
							$alterIDs[$klasse] = $nodeID;
							$ags = null;
						break;
						case "Wanderung":
							$wanderungIDs[$klasse] = $nodeID;
							$ags = null;
						break;
						case "Nation":
							$nationIDs[$klasse] = $nodeID;
							$ags = null;
						break;
					}
					
					$nodes[] = array($ags, $klasse, $name);
					
					$val = $sheet->getCellByColumnAndRow(0, ++$row)->getValue();
				}
			break; // NODES ============================================================
			case "RELS":
				// col	0		1		2		3
				// 		start	end		type	d:int

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
			case "A": 
				// GEMEINDEDATEN BIS 2009
				// col	0		1			2			3			4			5
				//		AGS		Gemeinde	Fläche km2	Bevölkerung		
				//										insgesamt	männlich	weiblich
				
				// Datums-Knoten suchen
				$dateNodeID = getOrCreateDate( $sheetInfo[0] );
				$row = 5;
				
				$val = $sheet->getCellByColumnAndRow(0, $row)->getValue();
				while (!empty($val)){
					
					
					$land = substr($val,0,2);
					$landNodeID = @$spaceIDs[$land];
					if (!$landNodeID) {
						// Kreis existiert noch nicht und muss angelegt werden
						echo "\nFEHLER: Land $land existiert nicht.";
					}
					
					$kreis = substr($val,0,5);
					$kreisNodeID = @$spaceIDs[$kreis];
					if (!$kreisNodeID) {
						// Kreis existiert noch nicht und muss angelegt werden
						echo "\nFEHLER: Kreis $kreis existiert nicht.";
					}
					
					
					$ags = $val;
					$name = $sheet->getCellByColumnAndRow(1, $row)->getValue();
					
					$gemeindeNodeID = @$spaceIDs[$ags];
					if (!$gemeindeNodeID) {
						// Gemeinde existiert noch nicht und muss angelegt werden
						$nodeID++;
						$nodes[] = array($ags, null, $name);
						$gemeindeNodeID = $nodeID;
						$spaceIDs[$ags] = $nodeID;
					}
					
					// Relation zum Kreis anlegen
					$relations[] = array($kreisNodeID, $gemeindeNodeID, $relTypeAdmin, null, null, $sheetInfo[0]);
					
					// Relation zu Daten anlegen
					/*
					$relations[] = array($gemeindeNodeID, $flID,
								$relTypeFl, $sheet->getCellByColumnAndRow(2, $row)->getValue(),
								null, $sheetInfo[0]);
					$relations[] = array($gemeindeNodeID, $bevID,
								$relTypeBev, $sheet->getCellByColumnAndRow(3, $row)->getValue(),
								null, $sheetInfo[0]);
					*/
					storeFlaeche($gemeindeNodeID, $sheetInfo[0], $sheet->getCellByColumnAndRow(2, $row)->getValue());
					storeBev($gemeindeNodeID, $sheetInfo[0], "1", $sheet->getCellByColumnAndRow(4, $row)->getValue());
					storeBev($gemeindeNodeID, $sheetInfo[0], "2", $sheet->getCellByColumnAndRow(5, $row)->getValue());
					
					$val = $sheet->getCellByColumnAndRow(0, ++$row)->getValue();
				}
				
			break; // A ================================================================
			case "B": 
				// GEMEINDEDATEN AB 2010
				//	A		B		...																						...		N
				//	0		1		2		3	4		5	6	7				8		9			10			11			12		13
				//	Satzart	Textk.	Regionalschlüssel (RS)		Gemeindename	Fläche	Bevölkerung	-			-			-		PLZ
				//					Land	RB	Kreis	VB	Gem							insgesamt	männlich	weiblich	je km2	
				$row = 5;
				
				// Datums-Knoten suchen
				$dateNodeID = getOrCreateDate( $sheetInfo[0] );
				
				$emptyCount = 0;
				while ($emptyCount<10){
					$satzArt = $sheet->getCellByColumnAndRow(0, ++$row)->getValue();
					
					$land = $sheet->getCellByColumnAndRow(2, $row)->getValue();
					$landNodeID = @$spaceIDs[$land];
					
					$kreis = $land
							.$sheet->getCellByColumnAndRow(3, $row)->getValue()
							.$sheet->getCellByColumnAndRow(4, $row)->getValue();
					$kreisNodeID = @$spaceIDs[$kreis];
					
					$name = $sheet->getCellByColumnAndRow(7, $row)->getValue();
					
					switch ($satzArt) {
						case 60: // Gemeinde
							$ags = $kreis
									.$sheet->getCellByColumnAndRow(6, $row)->getValue();
							
							if (!$landNodeID || !$kreisNodeID) {
								// Kreis existiert noch nicht und muss angelegt werden
								echo "\nFEHLER: Land $land oder Kreis $kreis existieren nicht. $ags wird ignoriert.";
							} else {
							
								$gemeindeNodeID = @$spaceIDs[$ags];
								if (!$gemeindeNodeID) {
									// Gemeinde existiert noch nicht und muss angelegt werden
									$nodeID++;
									$nodes[] = array($ags, null, $name);
									$gemeindeNodeID = $nodeID;
									$spaceIDs[$ags] = $nodeID;
								}
								// Relation zum Kreis anlegen
								$relations[] = array($kreisNodeID, $gemeindeNodeID, $relTypeAdmin, null, null, $sheetInfo[0]);
								
								// Relation zu Daten anlegen
								/*
								$relations[] = array($gemeindeNodeID, $flID,
											$relTypeFl, $sheet->getCellByColumnAndRow(8, $row)->getValue(),
											null, $sheetInfo[0]);
								$relations[] = array($gemeindeNodeID, $bevID,
											$relTypeBev, $sheet->getCellByColumnAndRow(9, $row)->getValue(),
											null, $sheetInfo[0]);
								*/
								storeFlaeche($gemeindeNodeID, $sheetInfo[0], $sheet->getCellByColumnAndRow(8, $row)->getValue());
								storeBev($gemeindeNodeID, $sheetInfo[0], "1", $sheet->getCellByColumnAndRow(10, $row)->getValue());
								storeBev($gemeindeNodeID, $sheetInfo[0], "2", $sheet->getCellByColumnAndRow(11, $row)->getValue());
							}
							$emptyCount = 0;
						break;
						
						
						
						case 40: // Kreis
							if (!$kreisNodeID) {
								$kreisNodeID = getAndCreateKreis($kreis, $name, $sheetInfo[0]);
							}
							$emptyCount = 0;
						break;
						
						
						
						case 10: // Land
							if (!$landNodeID) {
								$landNodeID = getAndCreateLand($land, $name, $sheetInfo[0]);
							}
						break;
						default:
							$emptyCount++;
					}
				}
			
			break; // B ================================================================
			case "C":
				// KREISDATEN 19XX-2003
				// 0		1		2					3			4			5			6			7
				// Schlüssel-nummer	Kreisfreie Stadt	Fläche km2	Bevölkerung	-			-			-		
				//					Kreis / Landkreis				insgesamt	männlich	weiblich	je km2
				$emptyCount = 0;
				$row = 2;
				while ($emptyCount<10){
					$id = $sheet->getCellByColumnAndRow(0, ++$row)->getValue();
					$id = str_replace(" ","", $id); // Leerzeichen filtern
					if (!empty($id)) {
						$emptyCount = 0;
						switch (strlen($id)) {
							case 5:	// Kreise
								$name = $sheet->getCellByColumnAndRow(2, $row)->getValue();
								if ($name) {
									$kreisNodeID = getAndCreateKreis($id, $name, $sheetInfo[0]);
								}
								// Bei Kreis Einwohner und Fläche speichern?
							break;
							case 2: // Länder
								$name = $sheet->getCellByColumnAndRow(1, $row)->getValue();
								if ($name) {
									$landNodeID = getAndCreateLand($id, $name, $sheetInfo[0]);
								}
							break;
						}
					} else {
						// Zähle Zeilen ohne Eintrag bei der ID hoch.
						$emptyCount++;
					}
				}
			break; // C ================================================================
		}
		
		// Aufräumen
		$xls->disconnectWorksheets();
		unset( $sheet );
		unset( $xls );
		
		// Schreibe Nodes und Relations
		$nodeFile = fopen($outputFolder.'nodes.csv','a');
		foreach( $nodes as $node) {
			fwrite( $nodeFile, "\n".implode("\t", $node));
		}
		// Schließe Ausgabedateien
		fclose($nodeFile);


		// Öffne Ausgabedatei
		$relFile = fopen($outputFolder.'rels.csv','a');
		foreach( $relations as $rel) {
			fwrite( $relFile, "\n".implode("\t", $rel));
		}
		// Schließe Ausgabedatei
		fclose($relFile);

		// Arbeitsspeicher aufräumen
		unset($nodes);
		unset($relations);

		echo "OK.";
	}
	unset( $xlsReader );
	
	
}




// returns Node-ID for Land
function getAndCreateLand($id, $name, $date) {
	global $relTypeAdmin, $nodeID, $spaceIDs, $nodes, $relations;
	$landNodeID = @$spaceIDs[$id];
	if (!$landNodeID) {
		// Land existiert noch nicht und muss angelegt werden
		$nodeID++;
		$nodes[] = array($id, null, $name);
		$landNodeID = $nodeID;
		$spaceIDs[$id] = $nodeID;
	}
	// Relation zu DE anlegen
	$relations[] = array($spaceIDs['DE'], $landNodeID, $relTypeAdmin, null, null, $date);
	return $landNodeID;
}

function getAndCreateKreis($id, $name, $date) {
	global $relTypeAdmin, $nodeID, $spaceIDs, $nodes, $relations;
	$kreisNodeID = @$spaceIDs[$id];
	if (!$kreisNodeID) {
		// Kreis existiert noch nicht und muss angelegt werden		
		$nodeID++;
		$nodes[] = array($id, null, $name);
		$kreisNodeID = $nodeID;
		$spaceIDs[$id] = $nodeID;	
	}	
	// Relation zum Land anlegen
	$landNodeID = @$spaceIDs[(substr($id,0,2))];
	if (!$landNodeID) {
		echo "\nFEHLER: Land $id existiert nicht.";
	}
	$relations[] = array($landNodeID, $kreisNodeID, $relTypeAdmin, null, null, $date);
	return $kreisNodeID;
}

function getOrCreateDate( $date ) {
	global $nodeID, $dateIDs, $nodes, $relations;
	$dateNodeID = @$dateIDs[$date];
	if (!$dateNodeID) {
		$nodeID++;
		$nodes[] = array( null, $date, $date );
		$dateIDs[$date] = $nodeID;
		$dateNodeID = $nodeID;
		
		$relations[] = array($dateIDs['node'], $dateNodeID, "KLASSE", null, null, 0);
	}
	return $dateNodeID;
}

function storeFlaeche($spaceNodeID, $date, $flaeche) {
	global $nodeID, $dateIDs, $nodes, $relations, $flID;
	$dateNodeID = getOrCreateDate($date);
	
	// Merkmal-Node erzeugen
	$nodeID++;
	$nodes[] = array(null, null, "M");
	$merkmalNodeID = $nodeID;
	
	// Indikator-Relation anlegen
	$relations[] = array( $flID, $merkmalNodeID, "MERKMAL", null, null, 0 );
	// Date-Relation anlegen
	$relations[] = array( $merkmalNodeID, $dateNodeID, "MERKMALSWERT", null, null, 0 );
	// Space-Relation mit Wert anlegen
	$relations[] = array( $merkmalNodeID, $spaceNodeID, "WERT", $flaeche, null, 0 );
}

function storeBev($spaceNodeID, $date, $geschl_klasse, $geschl_wert) {
	global $nodeID, $dateIDs, $nodes, $relations, $geschlechtIDs, $bevID;
	$dateNodeID = getOrCreateDate($date);
	
	// Merkmal-Node erzeugen
	$nodeID++;
	$nodes[] = array(null, null, "M");
	$merkmalNodeID = $nodeID;
	
	// Indikator-Relation anlegen
	$relations[] = array( $bevID, $merkmalNodeID, "MERKMAL", null, null, 0 );
	// Date-Relation anlegen
	$relations[] = array( $merkmalNodeID, $dateNodeID, "MERKMALSWERT", null, null, 0 );
	// Geschlecht-Relation anlegen
	$relations[] = array( $merkmalNodeID, $geschlechtIDs[$geschl_klasse], "MERKMALSWERT", null, null, 0 );
	// Space-Relation mit Wert anlegen
	$relations[] = array( $merkmalNodeID, $spaceNodeID, "WERT", $geschl_wert, null, 0 );
}

// Bearbeite nun Wanderungsdaten
//include('aufbereitung_wanderung.php');

echo( "\n\nEnde der Aufbereitung.\n");
?>