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

require_once "config.inc.php";
require_once "includes.inc.php";

$log = new ErrorLog(THERMOSTATPI_LOG_FILE, THERMOSTATPI_LOG_FILE);

$toy = new PHP_IR_Toy\phpIRToy(IRTOY_DEVICE_PATH, PHPIRTOY_LOG_FILE);

$tpiDB = new ThermostatPiDB($log);

$codeFile = $_GET['file'];

$log->error_log("In Execute: " . $codeFile);

// Return payload
$returnPayload = array();

// Check if the code file exists!
if (file_exists(IR_CODES_PATH . $codeFile)) {
	$log->error_log("File Exists");
	$returnPayload["file_exists"] = true;

	// Now we know the files exists, let's execute it!
	$handle = fopen(IR_CODES_PATH . $codeFile, "r");
	if(!$handle) {
		$log->error_log("Could not open code file. Exiting.");
		$returnPayload["file_open_status"] = false;
	}
	$returnPayload["file_open_status"] = true;
	
	$contents = fread($handle, filesize(IR_CODES_PATH . $codeFile));
	fclose($handle);

	$log->error_log("Code loaded, transmitting.");
	$transmitReturn = $toy->transmit($contents);

	if ($transmitReturn) {
		$log->error_log("Transmit Successful.");
		$returnPayload["transmit_status"] = true;
		$tpiDB->logCodeCommand($codeFile,1);
	} else {
		$log->error_log("Transmit Not Successful.");
		$returnPayload["transmit_status"] = false;
		$tpiDB->logCodeCommand($codeFile,0);
	}
} else {
	$log->error_log("ERROR - File does not exist. Returning.");
	$returnPayload["file_exists"] = false;
	$returnPayload["transmit_status"] = false;
	$returnPayload["file_open_status"] = false;
}



echo json_encode($returnPayload);

?>