<?php
require_once( './lib/php-excel-reader-2.21/excel_reader2.php');
require("phar://lib/neo4jphp.phar");

use Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Cypher;
	
$client = new Client();
?>