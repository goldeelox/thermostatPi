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


// Loop until we want to quit.
do {
	
	print "Type in description of command: ";
	$line = trim(fgets(STDIN)); // reads one line from STDIN
	$line = str_replace(" ", "_", $line);
	
	$file = fopen("codes/" . $line . ".txt", 'w');

	print "press a button on remote...\n";
	$button_press = $toy->receive();
	$log->error_log("Received Button Press");
	$log->error_log(print_r($button_press, true));
	
	fwrite($file, print_r($button_press,true));
	
	fclose($file);
	
} while (true);






?>