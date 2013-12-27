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

$log = new ErrorLog();

$toy = new PHP_IR_Toy\phpIRToy(IRTOY_DEVICE_PATH);

// Get list of codes from directory "codes"

$files = scandir("codes/");
print_r($files);

print "Select code file to load: ";
fscanf(STDIN, "%d\n", $number); // reads number from STDIN

// Check if the input value is not acceptable
if ($number > count($files)) {
	print "Could not open code file. Exiting.\n";
	exit;
}

$handle = fopen("codes/" . $files[$number], "r");
if(!$handle) {
	print "Could not open code file. Exiting.\n";
	exit;
}
$contents = fread($handle, filesize("codes/" . $files[$number]));
fclose($handle);

print "Transmitting...\n";
$transmitReturn = $toy->transmit($contents);

if ($transmitReturn) {
	print "Transmit Successful.\n";
} else {
	print "Transmit Not Successful.\n";
}

?>