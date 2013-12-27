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

?>

<html>
	<head>
		<meta charset="utf-8" />
		
		<!-- Set the viewport width to device width for mobile -->
		<meta name="viewport" content="width=device-width" />
		
		<title>ThermostatPi</title>
		
		<link rel="stylesheet" href="stylesheets/foundation.css">
		<link rel="stylesheet" href="stylesheets/app.css">
		
		<!-- Custom Modernizr for Foundation -->
		<script src="javascripts/modernizr.foundation.js"></script>
	</head>
	<body>
		<!-- Latest version of jQuery -->
		<script src="javascripts/jquery.js"></script>
		
		<!-- Included JS Files (Minified) -->
		<script src="javascripts/foundation.min.js"></script>
		
		<!-- Initialize JS Plugins -->
		<script src="javascripts/app.js"></script>
		
		<script src="javascripts/thermostatpi.temperature.ajax.js"></script>
		
		<!-- Header -->
		<div class="row header">
			<div class="twelve columns">
				ThermostatPi
			</div>
		</div>
		
		<!-- Body -->
		<div class="row">
			<div class="twelve mobile-four columns">
				<br />
				<!-- Tabs -->
				<dl class="tabs contained mobile">
					<dd class="active"><a href="#tempStatusView">Status</a></dd>
					<dd><a href="#tempControlView">Controls</a></dd>
				</dl>
				
				<ul class="tabs-content contained ">
					<li class="active" id="tempStatusViewTab">
						<div class="row">
							<div class="twelve mobile-four columns">
								<h1><small>Temperature Status</small></h1>
							</div>
						</div>
						<div class="row">
							<div class="three mobile-two columns">
								<strong>Current Temperature:</strong>
							</div>
							<div class="three mobile-two columns end">
								<div class="currentTemperatureContainer"></div>
							</div>
						</div>
						<div class="row">
							<div class="three mobile-two columns">
								<strong>Last Updated:</strong>
							</div>
							<div class="three mobile-two columns end">
								<div id="currentTime"></div>
							</div>
						</div>
						<div class="row">
							<div class="twelve mobile-four columns">
								<h3><small>Hourly History Chart</small></h3>
							</div>
						</div>
						<div class="row">
							<div class="twelve mobile-four columns">
								<div class="hourlyChart"><img id="hourlyChart" src="rrdgraphs/hour.png"/></div>
							</div>
						</div>
						<div class="row">
							<div class="twelve mobile-four columns">
								<h3><small>Daily History Chart</small></h3>
							</div>
						</div>
						<div class="row">
							<div class="twelve mobile-four columns">
								<div class="dailyChart"><img id="dailyChart" src="rrdgraphs/day.png"/></div>
							</div>
						</div>
						
					</li>
					<li id="tempControlViewTab">
						<div class="row">
							<div class="twelve mobile-four columns">
								<h1><small>Temperature Controls</small></h1>
							</div>
						</div>
						<div class="row">
							<div class="twelve mobile-four columns">	
								<?php /*
									if($toy->getStatus()) { 
										?>
										<div class="alert-box success">
											IR Toy Device Enabled
										</div>
										<?php
										$log->error_log("Valid IR Device Enabled");
									} else { 
										?>
										<div class="alert-box alert">
											IR Toy Device Not Found / Initiation Failed
										</div>
										<?php
										$log->error_log("Valid IR Device Not Found");
									} 
								*/?>
							</div>
						</div>
						
						<div class="row">
							<div class="six mobile-four columns">
								<div class="panel radius">
									<div class="row">
										<div class="twelve mobile-four columns">
											<h3><small>Available Commands</small></h3>
										</div>
									</div>
									<div class="row">
										<div class="twelve mobile-four columns">
											<div class="alert-box success" id="executeCommandSuccessAlert"><!-- Success Text --></div>
											<div class="alert-box alert" id="executeCommandFailAlert"><!-- Failure Text --></div>
										</div>
									</div>
									<div class="row">
										<div class="twelve mobile-four columns" id="executeCodeAjaxLoader">
											<img src="images/ajax-loader.gif"><br /><br />
										</div>
									</div>
									<div class="row">
										<div class="twelve mobile-four columns">
											<?= getCodeCommands(); ?>
										</div>
									</div>
								</div>
							</div>
							<div class="six mobile-four columns">
								<div class="panel radius">
									<div class="row">
										<div class="twelve mobile-four columns">
											<h3><small>Command History</small></h3>
										</div>
									</div>
									<div class="row">
										<div class="twelve mobile-four columns">
											<div class="command_history"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
					</li>
				</ul>
				
				
			</div>
		</div>
	
		

	</body>
</html>