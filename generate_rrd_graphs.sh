 # ThermostatPi - a web application that can be used to manage a wall-mounted air conditioning
 # https://bitbucket.org/cdrum/thermostatpi/
 #
 # Copyright 2013 Chris Drumgoole <cdrum@cdrum.com> http://www.cdrum.com
 #
 # ----
 #
 # This file is part of ThermostatPi.
 #
 # ThermostatPi is free software: you can redistribute it and/or modify
 # it under the terms of the GNU General Public License as published by
 # the Free Software Foundation, either version 3 of the License, or
 # (at your option) any later version.
 # 
 # ThermostatPi is distributed in the hope that it will be useful,
 # but WITHOUT ANY WARRANTY; without even the implied warranty of
 # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 # GNU General Public License for more details.

 # You should have received a copy of the GNU General Public License
 # along with ThermostatPi.  If not, see <http://www.gnu.org/licenses/>.


#!/bin/bash
RRDPATH="/home/pi/thermostatpi/"
RAWCOLOUR="#FF0000"
RAWCOLOUR2="#CC0000"
TRENDCOLOUR="#0000FF"
TRENDCOLOUR2="#0000CC"

# Edited 2012/12/9 to add running averages to hourly and daily graphs

#hour
rrdtool graph $RRDPATH/rrdgraphs/hour.png --alt-autoscale --start -6h \
DEF:temp1=$RRDPATH/rPItemp.rrd:temp1:AVERAGE \
CDEF:trend1=temp1,1800,TREND \
LINE2:temp1$RAWCOLOUR:"Hourly Temperature" \
LINE1:trend1$TRENDCOLOUR:"30m Average"

#day
rrdtool graph $RRDPATH/rrdgraphs/day.png --alt-autoscale --start -1d \
DEF:temp1=$RRDPATH/rPItemp.rrd:temp1:AVERAGE \
CDEF:trend1=temp1,21600,TREND \
LINE2:temp1$RAWCOLOUR:"Daily Temperature" \
LINE1:trend1$TRENDCOLOUR:"6h Average"

#week
rrdtool graph $RRDPATH/rrdgraphs/week.png --start -1w \
DEF:temp1=$RRDPATH/rPItemp.rrd:temp1:AVERAGE \
LINE2:temp1$RAWCOLOUR:"Weekly Temperature"

#month
rrdtool graph $RRDPATH/rrdgraphs/month.png --start -1m \
DEF:temp1=$RRDPATH/rPItemp.rrd:temp1:AVERAGE \
LINE1:temp1$RAWCOLOUR:"Monthly Temperature"

#year
rrdtool graph $RRDPATH/rrdgraphs/year.png --start -1y \
DEF:temp1=$RRDPATH/rPItemp.rrd:temp1:AVERAGE \
LINE1:temp1$RAWCOLOUR:"Yearly Temperature"
