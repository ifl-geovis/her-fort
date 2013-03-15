<?php

class XLS {
	private $xls;
	private $worksheet;
	
	private $simpleReader=false;
	
	function __construct($dateiname, $sheetName) {
		mb_internal_encoding("UTF-8");
		mb_regex_encoding("UTF-8");
		
		if ($this->simpleReader) {
			require_once( './lib/php-excel-reader-2.21/excel_reader2.php');
			
			$this->xls = new Spreadsheet_Excel_Reader($dateiname,false,'UTF-8');
			$this->xls->setOutputEncoding('UTF-8');//ISO-8859-1');
			$this->xls->setUTFEncoder('mb');
		
		} else {
			require_once( './lib/PHPExcel/PHPExcel/IOFactory.php');
			$reader = PHPExcel_IOFactory::createReader('Excel5');
			
			$reader->setReadDataOnly(true);
			$reader->setLoadSheetsOnly($sheetName);
			
			$this->xls = $reader->load( $dateiname );
			
			$this->worksheet = $this->xls->getSheetByName($sheetName);
			
			unset( $reader );
		}
	}
	
	function val ($row, $col, $sheetIndex=0) {
		if ( $this->simpleReader ) {
			return $this->xls->raw($row,$col,$sheetIndex);
		} else {
			//return $this->worksheet->getCellByColumnAndRow($col-1, $row)->getCalculatedValue();
			return $this->worksheet->getCellByColumnAndRow($col-1, $row)->getValue();
		}
		
	}
	
	function rowcount() {
		return $this->worksheet->getHighestRow();
	}
	
	function __destruct() {
		if ( !$this->simpleReader ) {
			$this->xls->disconnectWorksheets();
		}
		unset ($this->xls);
	}
}
?>