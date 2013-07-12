<?php

$sourceFolder = "sourcedata/";
//$outputFolder = "importdata/";







require_once './lib/PHPExcel/PHPExcel/IOFactory.php';
require_once( './sourcedata/formate.php');
require("phar://lib/neo4jphp.phar");
//require_once('./lib/XLS.php');

// INIT Excel-Reader
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");
$schluessel=array();
$tableCount=1;
foreach($dateiformate as $fileName=>$sheets) {
	$xlsReader = PHPExcel_IOFactory::createReader('Excel5');
	$xlsReader->setReadDataOnly(true);
	//$tableCount=1;
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
		
			case "D": 
				// Schlüsselbrücken 2000-2011
				// col	0		1			        2			3			4			5		6		7		8		9		10		11		12
				//		LFD		Verwaltungseinh.	RS1	        AGS1		Name1		Art		Fläche	EW		RS2		AGS2	Name2	D_jur	D_stat
				
				// Datums-Knoten suchen
				//$dateNodeID = getOrCreateDate( $sheetInfo[0] );
				$aenderungsCount=0;
				$emptyCount = 0;
				$row = 7;
				$file = substr($fileName,0,4);
				$fileFirst = 2000;
				while ($emptyCount<10){
					$verw=$sheet->getCellByColumnAndRow(1, $row)->getValue();
					$ags1=$sheet->getCellByColumnAndRow(3, $row)->getValue();
					$name1=$sheet->getCellByColumnAndRow(4, $row)->getValue();
					$art=$sheet->getCellByColumnAndRow(5, $row)->getValue();
					$ags2=$sheet->getCellByColumnAndRow(9, $row)->getValue();
					$name2=$sheet->getCellByColumnAndRow(10, $row)->getValue();
					$date_jur=$sheet->getCellByColumnAndRow(11, $row)->getValue();
					$date_stat=$sheet->getCellByColumnAndRow(12, $row)->getValue();
					//Datumsformate umwandeln
					if(strpos($date_stat,$file)===false){
						$datum= ($date_stat - 25569)*86400;
						$date = gmdate("Ymd", $datum);
					}else{
						$date=(substr($date_stat,6,4)).(substr($date_stat,3,2)).(substr($date_stat,0,2));
						}
					
					
					//relevante Daten in Array einlesen
					switch ($verw){
						case "Gemeinde":
						  
						  $id=$ags2.((int)$fileFirst+$tableCount);
						  $id2=$ags1.(int)$file;
						  if($ags1!=$ags2 || $name1!=$name2){
						   if(strpos($name1, 'gemfr. Gebiete')===false){
						     if(strpos($name1, 'unbewohnt')===false){ 
								if (array_key_exists($id2, $schluessel)){
									$schluessel[$id2]["AGS"][]=$ags2;
									$schluessel[$id2]["Name"][]=$name2;
									$schluessel[$id2]["Aenderungsart"][]=$art;
									$schluessel[$id2]["Datum"][]=$date;
									echo"\n".$id2." gibts schon";
								}else{
									$schluessel[$id]=array ("AGS"=>array($ags1,$ags2),
																"Name"=>array($name1,$name2),
																"Aenderungsart"=>array($art),
																"Datum"=>array($date));	
								}
							$aenderungsCount++;
							}
						   }
						  }													  
						  $emptyCount = 0;
						  
						break;
						
						case "Gemeindeverband":
						  $emptyCount = 0;
						break;
						
						case "Region":
						  $emptyCount = 0;
						break;
						
						case "Kreis":
						  $emptyCount = 0;
						break;
						
						case  "Land":
						  $emptyCount = 0;
						break;
						
						case "Regierungsbezirk":
						  $emptyCount = 0;
						break;
						
						default:
						  $emptyCount++;
					}					
					$row++;

				}
				$emptyCount++;

		}
		echo("\n".$aenderungsCount." Aenderungen");
		unset( $xlsReader );
		
	}
	if(substr($fileName,0,5)=="2009I"){
		$tableCount= $tableCount;
	}else{
		$tableCount++;
	}
	echo("\n".$tableCount);
}
echo"\n";
print_r($schluessel);
echo( "\n\nEnde der Aufbereitung.\n");
?>