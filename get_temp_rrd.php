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

/*
 * Simple script to get the temperature reading from multiple devices (max 2) and store in rrd, echo to screen
 */

require "config.inc.php";

$resultArray = array();
$temperatures = array();

// Get current time
$currentTime = time();
$resultArray["timestamp"] = (string)$currentTime;
$resultArray["temperatures"] = array();

// Get devices array (device ids from 1 wire bus)
$devicesArray = file(THERMOMETER_DEVICES_PATH, FILE_IGNORE_NEW_LINES);

// Loop through each resource to get the reading
foreach($devicesArray as $deviceCode) {
	
	$devicePath = "/sys/bus/w1/devices/" . $deviceCode . "/w1_slave";
	
	// Open resource file for thermometer
	$thermometer = fopen($devicePath, "r");

	// Get the contents of the resource
	$thermometerReadings = fread($thermometer, filesize($devicePath));

	// Close resource file for thermometer
	fclose($thermometer);
	
	// We're only interested in the 2nd line, and the value after the t= on the 2nd line
	preg_match("/t=(.+)/", preg_split("/\n/", $thermometerReadings)[1], $matches);
	$temperature = $matches[1] / 1000;
	
	// Construct the temperature array for this device
	$payload = array();
	$payload["device"] = (string)$deviceCode;
	$payload["temperature"] = (string)$temperature;
	$temperatures[] = $temperature;
	
	// Add the device's temperature payload to the result array
	$resultArray["temperatures"][] = $payload;
}


// Save temp in rrd (2 values only for now...)
$temp1 = $temperatures[0];
$temp2 = $temperatures[1];
system("/usr/bin/rrdtool update  /home/cdrum/thermostatpi/rPItemp.rrd N:$temp1:$temp2");

echo json_encode($resultArray);
echo "\n";

?>