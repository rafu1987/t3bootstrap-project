<?php
/*
* phpMyImporter
* -------------
* Version: 1.00
* Copyright (c) 2009 by Micky Holdorf
* Adjusted for TYPO3 - 2012 by Raphael Zschorsch (zschorsch@medialis.net)
* Holdorf.dk/Software - micky.holdorf@gmail.com
* GNU Public License http://opensource.org/licenses/gpl-license.php
*
*/

class phpMyImporter {
	/**
	* @access private
	*/
	var $database = null;
	var $connection = null;
	var $compress = null;
	var $utf8 = null;

	var $importFilename = null;

	/**
	* Class constructor
	* @param string $db The database name
	* @param string $connection The database connection handler
	* @param boolean $compress It defines if the output/import file is compressed (gzip) or not
	* @param string $filepath The file where the dump will be written
	*/
	function phpMyImporter($db=null, $connection=null, $filepath='dump.sql', $compress=false) {
		$this->connection = $connection;
		$this->compress = $compress;
		$this->importFilename = $filepath;

		$this->utf8 = true;

		return $this->setDatabase($db);
	}

	/**
	* Sets the database to work on
	* @param string $db The database name
	*/
	function setDatabase($db){
		$this->database = $db;
		if ( !@mysql_select_db($this->database) )
			return false;
		return true;
  	}
	
	/**
	* Read from SQL file and make sql query
	*/
	function importSql($file) {
		// Reading SQL from file
		$content = "Reading SQL from file '".$this->importFilename."': ";
		if ($this->compress) {
			$lines = gzfile($file);
		}
		else {
			$lines = file($file);
		}
		$content .= " DONE!\n";
			
		$content .= "Importing SQL into database '".$this->database."': ";
		$x = 0;
		$importSql = "";
		$procent = 0;
		foreach ($lines as $line) {
			// Print progress
			$x++;
			$numOfLines = count($lines);
			if ($x%(int)($numOfLines/20) == 0) {
				$procent += 5;
				if ($procent%25 == 0) $content .= "$procent%";
				else $content .= ".";
			}

			// Importing SQL
			$importSql .= $line;
			if ( substr(trim($line), strlen(trim($line))-1) == ";" ) {
				$query = @mysql_query($importSql, $this->connection);
				if (!$query) return false;
				$importSql = "";
			}
		}
		//return $content;
	}
	
	/**
	* Import SQL file into selected database
	*/
	function doImport() {		
		if ( !$this->setDatabase($this->database) )
			return false;

		if ( $this->utf8 ) {
			$encoding = @mysql_query("SET NAMES 'utf8'", $this->connection);
		}

		if ( $this->importFilename ) {
			$import = $this->importSql($this->importFilename);
			if (!$import) $content .= "\n".mysql_error($this->connection)."\n";
			else $content .= " DONE!\n";
			//return $content . $import;
			return $import;
		}
		else {
			return false;
		}
	}
}
?>