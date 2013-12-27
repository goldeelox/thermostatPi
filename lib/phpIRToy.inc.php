<?php

/*
 * phpIRToy - a PHP Library for interfacing with the USB IR Toy
 * https://bitbucket.org/cdrum/phpirtoy/
 *
 * v1.1 16 February, 2013
 *
 * This is the core library file. Include it in your code. 
 *
 * See USB IR Toy v2 Documentation at following URLs:
 * 1. http://dangerousprototypes.com/docs/USB_IR_Toy:_IRman_decoder_mode
 * 2. http://dangerousprototypes.com/docs/USB_IR_Toy:_Sampling_mode
 *
 * The PHP Module Direct IO (dio) is required!
 *
 * Copyright 2013 Chris Drumgoole <cdrum@cdrum.com> http://www.cdrum.com
 *
 * Special thanks to Chris LeBlanc and his PyIRToy Python library.
 * This is a PHP-ported version (with some modifications) of his work.
 * https://github.com/crleblanc/PyIrToy
 *
 * ----
 *
 * This file is part of phpIRToy.
 *
 * phpIRToy is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * phpIRToy is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with phpIRToy.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/*
 * Defining a namespace to avoid name conflics
 */
namespace PHP_IR_Toy;

/*
 * Error Logging Class
 */
class ErrorLog {
	
	private $error_file;
	
	function __construct($errorfilepath = "/tmp/phpirtoy.log") {
		$this->error_file = $errorfilepath;
	}
	
	public function error_log($msg) { 
		$date = date('Y-m-d h:i:s'); 
		error_log("[" . $date . "] - " . $msg . "\n", 3, $this->error_file); 
	}
}

/* 
 * phpIRToy Class
 */
class phpIRToy {
	
	// Device
	private $toy;
	
	// Error Log Object
	private $log;
	
	// Internal Variables
	private $devicePath;
	private $sleepTime;
	private $handshake;
	private $transmitMode;
	private $firmwareVersion;
	private $status;
	
	/*
	 * Constructor
	 *
	 * Open the device, set the standard terminal attributes, including baud rate.
	 * Set the device to Sampling Mode
	 */
	function __construct($device, $logfile = "/tmp/phpirtoy.log") {
		
		// Initiate error logging
		$this->log = new ErrorLog($logfile);
		
		// Attempt to open device
		$this->toy = dio_open($device, O_RDWR | O_NOCTTY);
				
		$this->devicePath = $device;
		
		// If we fail in trying to open device, log it, and exit out.
		if (!$this->toy) {
			$this->log->error_log("Failed to open device " . $device . ". Exiting.");
			$this->status = false;
			return;
		} else {
			$this->log->error_log("Opened Device " . $device);
		}
				
		// 8-N-1
		dio_tcsetattr($this->toy, array(
		  'baud' => 9600,
		  'bits' => 8,
		  'stop'  => 1,
		  'parity' => 0
		)); 
		
		// Set internal sleep time
		$this->sleepTime = 50000; // 0.05 seconds
		
		// Get and store firmware version		
		$this->getFirmwareVersion();
		$this->log->error_log("IR Toy Firmware Version: " . $this->firmwareVersion);
		
		// Default set to sampling mode
		$this->setSamplingMode();
		
		$this->status = true;
	}
	
	/*
	 * Destructor
	 *
	 * Release the device
	 */
	function __destruct() {
	
		dio_close($this->toy);
		
		$this->status = false;
		
		$this->log->error_log("Closed Device " . $this->devicePath);
	}
	
	/*
	 * Enable Sampling Mode
	 */
	private function setSamplingMode() {
		
		$this->log->error_log("Attempting to set Sampling Mode");
		
		$this->reset();

		dio_write($this->toy, 's'); // Sampling Mode
		
		$this->sleep();
		
		$samplingModeResponse = dio_read($this->toy,3);
		
		if ($samplingModeResponse != "S01") {
			$this->log->error_log("ERROR - Sampling mode failed. Exiting.");
			exit;
		} else {
			$this->log->error_log("Success, we're now in Sampling Mode.");
		}
		
		$this->flashLED();	// For fun...
		
		$this->transmitMode = false;
	}
	
	/*
	 * Enable Transmit Mode
	 * (part of Sampling mode!)
	 */
	private function setTransmitMode() {
		
		$this->log->error_log("Attempting to set Transmit Mode");
		
		$this->reset();

		// First set to sampling mode
		dio_write($this->toy, 's'); // Sampling Mode

		dio_write($this->toy, "\x24"); // Transmit Byte Count Report		
		dio_write($this->toy, "\x25"); // Transmit Notify on Complete
		dio_write($this->toy, "\x26"); // Transmit Handshake

		dio_write($this->toy, "\x03"); // Transmit
		
		$this->log->error_log("Waiting for for transmit handshake...");
		$this->handshake = dio_read($this->toy, 1);

		$this->log->error_log("Transmit handshake: 0x" . bin2hex($this->handshake));
		
		$this->transmitMode = true;
	}
	
	/*
	 * Get firmware version from IR Toy
	 */
	private function getFirmwareVersion() {
		$this->reset();
		dio_write($this->toy, 'v'); // Get firmware version
		$this->firmwareVersion = dio_read($this->toy, 4);
	}
	
	/*
	 * Flash the LED on the board
	 * Just for fun
	 */
	private function flashLED() {
		dio_write($this->toy, "\x12"); // Turn on LED
		$this->sleep();
		dio_write($this->toy, "\x13"); // Turn off LED
	}
	
	/*
	 * Sleep...
	 */
	private function sleep() {
		usleep($this->sleepTime);
	}
	
	/*
	 * Reset board back to original state
	 */
	private function reset() {
		$this->sleep();
		dio_write($this->toy, "\x00");
		dio_write($this->toy, "\x00");
		dio_write($this->toy, "\x00");
		dio_write($this->toy, "\x00");
		dio_write($this->toy, "\x00");
	}
	
	/* 
	 * Returns true or false, depending on if constructor succeeded or not
	 */
	function getStatus() {
		return $this->status;
	}
	
	/*
	 * Receive IR codes from board
	 */
	function receive() {
		
		$this->log->error_log("In Receive");
		
		// Sleep
		$this->sleep();
		
		// Force sampling mode
		$this->setSamplingMode();
		
		// Config
		$bytesToRead = 1;
		$readCount = 0;
		$irCode = array();
		
		$this->log->error_log("Waiting for IR input...");
		
		// Loop to collect IR string
		while(true) {
			
			$readVal = dio_read($this->toy, $bytesToRead);
			$intVal = ord($readVal); // Convert to integer representation
						
			$irCode[] = $intVal;
						
			// Break out when we get 2 in a row with 255
			if ($readCount >= 2 && $intVal == 255 && $irCode[$readCount-1] == 255) {
				break;
			}
			
			$readCount++;
		}
				
		$this->reset();
		
		// Return the string captured
		$this->log->error_log("Read Complete");
		
		return json_encode($irCode);
	}
	
	/*
	 * Transmit JSON Encoded decimal represented code string
	 *
	 * It is safe to assume $code contains what was returned from the receive function.
	 *
	 * Returns TRUE on success
	 * Returns FALSE on failure
	 */
	function transmit($code) {
		
		$this->log->error_log("In Transmit");
		
		$this->setTransmitMode();
		$transmitCode = "";
		
		// Get code and decode the json format
		$code = json_decode($code);
		
		$codeLength = count($code);
		$this->log->error_log("Size of code to transmit: " . $codeLength);
		
		// Make sure length of code is at least 2 chars
		if ($codeLength < 2) {
			$this->log->error_log("ERROR - Code to transmit is not 2 or more. Returning.");
			$this->sleep(); $this->reset();
			return false;
		}
		
		// Make sure length of code is even
		if (($codeLength % 2) != 0) {
			$this->log->error_log("ERROR - Code to transmit is not even. Returning.");
			$this->sleep(); $this->reset();
			return false;
		}
		
		// Make sure last 2 chars is 255 (or xff). If not, we'll add it.
		if (($code[$codeLength-1] != 255) || ($code[$codeLength-2] != 255)) {
			$this->log->error_log("WARNING - 0xff 0xff not found at end, adding...");
			$code[] = 255;
			$code[] = 255;
			$codeLength+=2;
			$this->log->error_log("New size of code to transmit: " . $codeLength);
		}
				
		// Max length of send
		$maxTransmitBlockSize = 32;
		
		$this->log->error_log("Transmitting...");
		
		// Loop the code paylode for transmit
		do {			
			
			$rawTransmitCode = ""; // For logging
			$transmitCode = ""; // Code payload sub section
			
			// Determine current remaining number of codes to send
			$currentSizeOfCode = count($code);
			
			// Loop through each char, up to 32
			for ($y = 0; $y < (($currentSizeOfCode > $maxTransmitBlockSize)? $maxTransmitBlockSize : $currentSizeOfCode); $y++) {
				$byte = array_shift($code);
				$rawTransmitCode .= $byte . ", ";
				$transmitCode .= chr($byte);
			}
					
			$this->log->error_log("Transmit code block decimal values: " . $rawTransmitCode);

			// Transmit block
			$bytesSent = dio_write($this->toy, $transmitCode);
			$this->log->error_log("Sent " . $bytesSent . " codes.");
			
			$this->log->error_log("Waiting for transmit handshake...");
			$this->handshake = (dio_read($this->toy, 1));
			$this->log->error_log("Received transmit handshake: 0x" . bin2hex($this->handshake));
			
		} while (count($code));
		
		$this->sleep();
		
		// Get final 3 0x3e handshake responses - I know, weird. 3 shouldn't be here as per the docuentation...
		for ($i = 0; $i < 3; $i++) {
			$returnVal = dio_read($this->toy, 1);
			$this->log->error_log("This should be a 0x3e: 0x" . bin2hex($returnVal));
		}
		
		// Get the transmit byte count
		$tVal = dio_read($this->toy, 1); // t
		if ($tVal != "t") {
			$this->log->error_log("ERROR - 't' not returned when we expected it. Returning.");
			$this->sleep(); $this->reset();
			return false;
		}
				
		$returnVal = dio_read($this->toy, 2); // 2 bytes with count
		$sendCount = hexdec(bin2hex($returnVal));
		
		if ($sendCount != $codeLength) {
			$this->log->error_log("ERROR -  The number of bytes transmitted is incorrect: " . $sendCount . " vs " . $codeLength);
			$this->sleep(); $this->reset();
			return false;
		} else {
			$this->log->error_log("The correct number of bytes have been transmitted: " . $sendCount);
		}
		

		// Get final success / failure
		$transmitResult = dio_read($this->toy, 1);
		if ($transmitResult == "C") {
			$this->log->error_log("Transmit Successful - 'C' returned.");
		} else {
			$this->log->error_log("WARNING - Transmit Unsuccessful - 'C' not returned.");
			$this->log->error_log("Actual Transmit Result Val: " . $transmitResult);
			$this->sleep(); $this->reset();
			return false;
		}
		
		$this->sleep(); $this->reset();
		
		$this->log->error_log("Transmit Complete");
		
		return true;
	}

}

?>