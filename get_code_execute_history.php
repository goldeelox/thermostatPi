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
	
$tpiDB = new ThermostatPiDB($log);
	
$log->error_log("In get_code_execute_history");

$result = $tpiDB->loadCodeCommandHistory(6);
	
print json_encode($result);
?>