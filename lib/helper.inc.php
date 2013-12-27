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

function getCodeCommands() {
	
	$files = scandir(IR_CODES_PATH);
	$returnVal = "";
	
	if (!$files) {
		$returnVal = <<<EOF
<div class="alert-box alert">
	Could not access codes.
</div>
EOF;
	}
	
	if (count($files) < 1) {
		$returnVal = <<<EOF
<div class="alert-box alert">
	No Codes were found.
</div>
EOF;
	}
	
	// Loop through codes
	foreach($files as $codeFile) {
		// Skip the directories
		if (($codeFile == ".") || ($codeFile == "..")) {
			continue;
		}
		
		$codeFileName = $codeFile;
		
		$codeFile = str_replace("_", " ", $codeFile);
		$codeFile = str_replace(".txt", "", $codeFile);
		$codeFile = ucwords($codeFile);
		
		$returnVal .= <<<EOF
<a class="small radius button codeExecuteBtn" id="$codeFileName" href="#">$codeFile</a><br />

EOF;
	}
	
	return $returnVal;

}

?>