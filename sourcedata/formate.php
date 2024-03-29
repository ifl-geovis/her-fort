<?php
// Aufstellung der f�r den Import der Gemeindedaten verwendeten XLS-Dateien
$dateiformate = array(

// FORMAT:
// 	Blattname				Datum			Tabellentyp

// Allgemeine Anwendungsdaten
// M�ssen zuerst abgearbeitet werden! Keine Dateien davor einf�gen!

/*"anwendungsdaten.xls"=>array(
	"nodes"		=>	array(	"",				"NODES" ),
	"rels"		=>	array(	"",				"RELS" ),
),*/

// Erfassung der Kreis- und L�nderdaten VOR 2010
// M�ssen vor den Gemeindedaten abgearbeitet werden! Keine Gemeindedaten davor einf�gen!
/*
"Kreisbericht_2000.xls"=>array(	"31.12.2000" =>	array(	"20001231",		"C")),
"Kreisbericht_2001.xls"=>array(	"31.12.2001" =>	array(	"20011231",		"C")),
"Kreisbericht_2002.xls"=>array(	"31.12.2002" =>	array(	"20021231",		"C")),
"Kreisbericht_2003.xls"=>array(	"31.12.2003" =>	array(	"20031231",		"C")),
"Kreisbericht_2004.xls"=>array(	"31.12.2004" =>	array(	"20041231",		"C")),
"Kreisbericht_2005.xls"=>array(	"31.12.2005" =>	array(	"20051231",		"C")),
"Kreisbericht_2006.xls"=>array(	"31.12.2006" =>	array(	"20061231",		"C")),
"Kreisbericht_2007.xls"=>array(	"31.12.2007_Jahr" =>	array(	"20071231",		"C")),
"Kreisbericht_2008.xls"=>array(	"04" =>	array(	"20081231",		"C")),
"Kreisbericht_2009.xls"=>array(	"31.12.2009" =>	array(	"20091231",		"C")),
*/
// Erfassung der Gemeindedaten

/*
// Diese Jahre werden im Projekt zur Zeit nicht ber�cksichtigt
"Gem_1990-2000.xls"=>array(
	"311290"	=>	array(	"19901231",		"A" ),
	"311291"	=>	array(	"19911231",		"A" ),
	"311292"	=>	array(	"19921231",		"A" ),
	"311293"	=>	array(	"19931231",		"A" ),
	"311294"	=>	array(	"19941231",		"A" ),
	"311295"	=>	array(	"19951231",		"A" ),
	"311296"	=>	array(	"19961231",		"A" ),
	"311297"	=>	array(	"19971231",		"A" ),
	"311298"	=>	array(	"19981231",		"A" ),
	"311299"	=>	array(	"19991231",		"A" ),
	"311200"	=>	array(	"20001231",		"A" )
),
*/

/*"Gem_2000-2011.xls"=>array(

	"311200"	=>	array(	"20001231",		"A" ),
	
	"311201"	=>	array(	"20011231",		"A" ),
	"311202"	=>	array(	"20021231",		"A" ),
	"311203"	=>	array(	"20031231",		"A" ),
	"311204"	=>	array(	"20041231",		"A" ),
	"311205"	=>	array(	"20051231",		"A" ),
	"311206"	=>	array(	"20061231",		"A" ),
	"311207"	=>	array(	"20071231",		"A" ),
	"311208"	=>	array(	"20081231",		"A" ),
	"311209"	=>	array(	"20091231",		"A" ),
	
	"311210"	=>	array(	"20101231",		"B" ),
	"311211"	=>	array(	"20111231",		"B" ),
	
),
*/
//Schl�sselbr�cken

"2000.xls"=>array(	"Gebietsaenderungen_2000" =>	array(	"20001231",		"D")),
"2001.xls"=>array(	"Gebietsaenderungen_2001" =>	array(	"20011231",		"D")),
"2002.xls"=>array(	"Gebietsaenderungen_2002" =>	array(	"20021231",		"D")),
"2003.xls"=>array(	"Gebietsaenderungen_2003" =>	array(	"20031231",		"D")),
"2004.xls"=>array(	"Gebietsaenderungen_2004" =>	array(	"20041231",		"D")),
"2005.xls"=>array(	"Gebietsaenderungen_2005" =>	array(	"20051231",		"D")),
"2006.xls"=>array(	"Gebietsaenderungen_2006" =>	array(	"20061231",		"D")),//den Tabname musste ich �ndern
"2007.xls"=>array(	"Gebietsaenderungen_2007" =>	array(	"20071231",		"D")),
"2008.xls"=>array(	"Gebietsaenderungen_2008" =>	array(	"20081231",		"D")),
"2009InklusiveUmstellungJanuar.xls"=>array(	"RS-Umstellung am 01.01.2009" =>	array(	"20090101",		"D")),
"2009JanuarDezmber.xls"=>array(	"Gebietsaenderungen_2009" =>	array(	"20091231",		"D")),
"2010.xls"=>array(	"Gebietsaenderungen 2010" =>	array(	"20101231",		"D")),
"2011.xls"=>array(	"Gebietsaenderungen 2011" =>	array(	"20111231",		"D"))
);

?>