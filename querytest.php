<?php

//require_once( './lib/php-excel-reader-2.21/excel_reader2.php');

require("phar://lib/neo4jphp.phar");

use Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Cypher;
	
$client = new Client();

$queryTemplate = "START n=node(*) RETURN max(ID(n)) AS maxNodeId";

$query = new Cypher\Query($client, $queryTemplate);//, array('title'=>$movie));
$result = $query->getResultSet();

echo "Found ".count($result)." row:\n";
foreach($result as $index=>$row) {
	echo "$index: ";
	foreach($row as $key=>$val) {
		echo "$key=$val ";
	}
	echo "\n";
}

?>