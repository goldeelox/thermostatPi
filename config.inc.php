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
 * Constants
 */

if (!defined("ROOT_PATH")) define("ROOT_PATH", "/var/www/thermostatpi/");

if (!defined("THERMOMETER_DEVICES_PATH")) define("THERMOMETER_DEVICES_PATH", "/sys/bus/w1/devices/w1_bus_master1/w1_master_slaves");
//if (!defined("THERMOMETER_SENSOR_PATH")) define("THERMOMETER_SENSOR_PATH", "/sys/bus/w1/devices/28-0000032837ea/w1_slave");
if (!defined("IRTOY_DEVICE_PATH")) define("IRTOY_DEVICE_PATH", "/dev/ttyACM0");
if (!defined("THERMOSTATPI_LOG_FILE")) define("THERMOSTATPI_LOG_FILE", ROOT_PATH . "log/thermostatpi.log");
if (!defined("PHPIRTOY_LOG_FILE")) define("PHPIRTOY_LOG_FILE", ROOT_PATH . "log/phpirtoy.log");
if (!defined("IR_CODES_PATH")) define("IR_CODES_PATH", ROOT_PATH . "codes/");

if (!defined("DB_FILE")) define("DB_FILE", ROOT_PATH . "db/thermostatpi.db");
?>
