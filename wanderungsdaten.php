<?php

//teste_spaltenerkennung();

function teste_spaltenerkennung() {
	$sourceFolder = "sourcedata/wander/";
	ini_set('auto_detect_line_endings',TRUE);
	$csvDateien = glob($sourceFolder."*.DBF.csv");
	foreach ($csvDateien as $dateiname) {
		//echo "\n \n Lese ".$dateiname."\n";
		$csvFile = fopen( $dateiname,'r');
		$header = fgetcsv( $csvFile );
		$meineSpalten = spaltenerkennung($header);
		//print_r($meineSpalten);
		fclose($csvFile);
	}
}

function spaltenerkennung ($header){
		$spalten = array(
			"JAHR" => -1,
			"SCHL" => -1,
			"ART" => -1,
			"SCHL1" => -1,
			"GESCHL" => -1,
			"ANZ" => -1,
			"AG" => -1,
			"FAM" => -1,
			"NAT" => -1
		);
		//print_r($header);
		foreach( $header as $key=>$value) {
			$kurzvalue = explode(",", $value);
			if (isset($spalten[$kurzvalue[0]])) {
				$spalten[$kurzvalue[0]] = $key;
			} else {
				//echo "Unbekannte Spalte gefunden: ".$value."\n";
				return null;
			}
			
		}
		$spaltenbesetzung = alleSpaltenBesetzt($spalten);
		//print_r($spaltenbesetzung);	
		//$tausch = headerspaltentausch($header, $spalten);
		//print_r($tausch);
	return $spalten;//ConfigFuerDieseCsv;
}

function alleSpaltenBesetzt($spalten) {
  if (in_array(-1, $spalten)) {
    //echo("es fehlen Spalten!\n");
	$fehlspalte = array_search(-1, $spalten);
	echo($fehlspalte."\n");
	return false;
  }else{
		//echo("Alle bekannten Spalten vorhanden!\n\n");
		return true;
		}
}

/*function headerspaltentausch($spalten) {
	foreach($spalten as $spaltenkey => $spaltenvalue){
		if($spaltenkey == $value){
			$key = $spaltenvalue;
		}
		print_r($header);
	}
	return $header;
}

function headerspaltentausch($header, $spalten) {
	foreach( $header as $key=>$value) {
			$kurzvalue = explode(",", $value);
			if (in_array($key, $spalten)) {
			$value = array_search($kurzvalue, $spalten);
			}
		
	}
	return $header;
}*/
?>

