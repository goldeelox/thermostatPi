<?php

/*
 * ThermostatPi - a web application that can be used to manage a wall-mounted air conditioning
 * https://bitbucket.org/cdrum/thermostatpi/
 *
 * Copyright 2013 Chris Drumgoole <cdrum@cdrum.com> http://www.cdrum.com
 *
 * ----
 *
 * This file is part of ThermostatPi.
 *
 * ThermostatPi is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * ThermostatPi is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with ThermostatPi.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

class ThermostatPiDB extends SQLite3
{	
	
	private $log = NULL;
	/*
	 * DB Schema:
	 */
	
    function __construct($log = NULL)
    {
		$this->open(DB_FILE);
		
		// Connect to or Set up DB
		$this->exec('CREATE TABLE IF NOT EXISTS history (code_command TEXT, date_added TEXT, status INTEGER)');    
		
		// Initiate error logging
		$this->log = $log;
    }

	function logCodeCommand($command, $status = 1) {
		
		$rt = $this->exec("INSERT OR REPLACE INTO history (code_command, date_added, status) VALUES ('" . $command . "', datetime('now', 'localtime'), '" . $status . "')");
		if ($this->log) {
			if ($rt) {
				$this->log->error_log("Added entry to history db for command '" . $command . "'");
			} else {
				$this->log->error_log("Attempted to add entry to history db for command '" . $command . "' but couldn't.");
			}
		}
	}
	
	function loadCodeCommandHistory($limit = 10, $orderField = "date_added", $orderModifier = "desc") {
		
		$results = $this->query("SELECT * FROM history order by " . $orderField . " " . $orderModifier . " LIMIT " . $limit);
		
		$this->log->error_log("in load");
		$resultArray = array();
		
		while ($row = $results->fetchArray()) {
			$tmp = array();
			$tmp["date_added"] = $row["date_added"];
			$codeFile = $row["code_command"];
			$codeFile = str_replace("_", " ", $codeFile);
			$codeFile = str_replace(".txt", "", $codeFile);
			$codeFile = ucwords($codeFile);
			$tmp["command_code"] = $codeFile;
			$tmp["status"] = ($row["status"])?"Success":"Fail";
			$resultArray[] = $tmp;
		}
		return $resultArray;
		
	}

}

?>
