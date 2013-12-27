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

;(function poll(){
	setTimeout(function(){
		$.ajax({                                      
			url: 'get_current_temp_json.php',
			data: "",
			context: document.body,
			dataType: 'json',
			
			success: function(data) {
				//alert("in success");
				//console.log('In Success');
				if (jQuery.isEmptyObject(data)) {
					// Empty... oh wel..
					
				} else {
					// Extract the timestamp first
					var date = new Date(data.timestamp*1000);
					var hours = date.getHours();
					var minutes = date.getMinutes();
					var seconds = date.getSeconds();

					// 12 hour clock
				        var ampm = hours >= 12 ? 'PM' : 'AM';
					hours = hours % 12;
					hours = hours ? hours : 12;
	
					// Prefix with 0 if less than 10
					if (hours < 10) {
						hours = "0" + hours;
					}
					if (minutes < 10) {
						minutes = "0" + minutes;
					}
					if (seconds < 10) {
						seconds = "0" + seconds;
					}
					
					// Concatinate Date and Time into a single string
					var formattedTime = hours + ':' + minutes + ':' + seconds + ' ' + ampm;
					$('#currentTime').text(formattedTime);
					
					// Loop through each temperature reading
					$('.currentTemperatureContainer').empty();
					$.each(data.temperatures, function(k, v) {
						$('.currentTemperatureContainer').append('<div id="' + v.device + '"></div>');
						$('#' + v.device).text(Math.round(v.temperature*10)/10  + '\u00B0' + " F");
					});
				}
				
			},
			error: function(data) {
				//alert("in error");
				//console.log('In Error');
				//console.log(data.responseText);							
			},
			complete: function() {
				//alert("in complete");
				//console.log('In Complete');
				poll();
			} 
		});
	}, 500); // Timeout 5 seconds
})();

$(document).ready(function() {
	
	// Hide ajax loader
	$('#executeCodeAjaxLoader').hide();
	
	// Hide execute code alerts
	$('#executeCommandSuccessAlert').hide();
	$('#executeCommandFailAlert').hide();
	$('#executeCommandFailAlert').empty();
	$('#executeCommandSuccessAlert').empty();
	
	function updateCharts() {
		d = new Date();
		$("#hourlyChart").attr("src", "rrdgraphs/hour.png?"+d.getTime());
		$("#dailyChart").attr("src", "rrdgraphs/day.png?"+d.getTime());
	}
	
	// Redraw the image every minute
	setInterval(function() {
		updateCharts();
	}, 60000);
	
	
	
	$('.codeExecuteBtn').click(function(e){
				
		// Show ajax loader
		$('#executeCodeAjaxLoader').show();
		
		// Clear out alert boxes
		$('#executeCommandFailAlert').hide();
		$('#executeCommandSuccessAlert').hide();
		$('#executeCommandFailAlert').empty();
		$('#executeCommandSuccessAlert').empty();
		
		function handleCodeExecuteBtnClk() {
			// Execute button click
			$.ajax({
				url: 'execute_code.php',
				data: 'file=' + $(e.target).attr('id'),
				dataType: 'json',
				
				success: function(data) {
					console.log('In Success');
					console.log('File Status: ' + data.file_exists);

					// Hide ajax loader
					$('#executeCodeAjaxLoader').hide();
					
					// Check if we had a successful transmit
					if (data.transmit_status) {
						// Success
						$('#executeCommandFailAlert').hide();
						$('#executeCommandSuccessAlert').show();
						$('#executeCommandSuccessAlert').append('Transmit Succeded!');
					} else {
						// Failure
						$('#executeCommandSuccessAlert').hide();
						$('#executeCommandFailAlert').show();
						$('#executeCommandFailAlert').append('Transmit Failed!');
					}
					draw_history_table();
				},
				error: function(data) {
					console.log('In Error');
					console.log('File Status: ' + data.file_exists);
					
					// Hide ajax loader
					$('#executeCodeAjaxLoader').hide();
					$('#executeCommandSuccessAlert').hide();
					$('#executeCommandFailAlert').show();
					$('#executeCommandFailAlert').append('Transmit Failed!');
				}
				
			});
		}
		handleCodeExecuteBtnClk();

	});
	
	function draw_history_table() {
		
		
		// Update history list
		$.ajax({
			url: 'get_code_execute_history.php',
			dataType: 'json',
			
			success: function(data) {
				console.log('Updating')
				// Check if data is empty or not (i.e. do we have alerts?)
				if (jQuery.isEmptyObject(data)) {
					// Empty
					console.log('There is no history to show.');
					$('.command_history').empty(); // Clear out output holding space
					$('.command_history').append('There are currently no alarms.');
				} else {
					// Not Empty
					console.log('We have entries to list.');
					$('.command_history').empty(); // Clear out output holding space
					
					$.each(data, function(k, v) {

						var uniqueRow1 = Math.floor((Math.random()*10000000)+1);

						$('.command_history').append("<div class='row' id='" + uniqueRow1 + "'>");
						$('#' + uniqueRow1).append("<div class='twelve mobile-four columns'><h6>" + v.command_code + "</h6></div>");

						var uniqueRow2 = Math.floor((Math.random()*10000000)+1);

						$('.command_history').append("<div class='row' id='" + uniqueRow2 + "'>");
						$('#' + uniqueRow2).append("<div class='six mobile-four columns'>" + v.date_added + "</div>");
						$('#' + uniqueRow2).append("<div class='six mobile-four columns'>" + v.status + "</div>");

					});				
				}
			}, 
			error: function(data) {
				
			}
		});	
	}
	draw_history_table();
	
});
