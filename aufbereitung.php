<?php
require_once( './lib/php-excel-reader-2.21/excel_reader2.php');
require("phar://lib/neo4jphp.phar");

use Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Cypher;
	
$client = new Client();



// Test, ob Datenbank leer ist
$query = new Cypher\Query($client, "START n=node(*) RETURN max(ID(n)) AS maxNodeId");
$result = $query->getResultSet();
$maxNodeID = $result[0]["maxNodeId"];
if ($maxNodeID>0) {
	// Abbruch des Skripts
	die("Die Datenbank ist nicht leer!");
}


// Öffne Ausgabedateien
$nodeFile = fopen('importdata/nodes.csv','w');
$relFile = fopen('importdata/rels.csv','w');

// Schließe Ausgabedateien
fclose($nodeFile);
fclose($relFile);


?>