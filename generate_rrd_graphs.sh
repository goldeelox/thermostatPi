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
RRDPATH="/home/cdrum/thermostatpi/"
RAWCOLOUR="#FF0000"
RAWCOLOUR2="#CC0000"
TRENDCOLOUR="#0000FF"
TRENDCOLOUR2="#0000CC"

# Edited 2012/12/9 to add running averages to hourly and daily graphs

#hour
rrdtool graph $RRDPATH/rrdgraphs/hour.png --upper-limit 30 --lower-limit 20 --rigid --start -6h \
DEF:temp1=$RRDPATH/rPItemp.rrd:temp1:AVERAGE \
DEF:temp2=$RRDPATH/rPItemp.rrd:temp2:AVERAGE \
CDEF:trend1=temp1,1800,TREND \
CDEF:trend2=temp2,1800,TREND \
LINE2:temp1$RAWCOLOUR:"Hourly Raspberry PI temperature - 1" \
LINE1:trend1$TRENDCOLOUR:"30 min average" \
LINE2:temp2$RAWCOLOUR2:"Hourly Raspberry PI temperature - 2" \
LINE1:trend2$TRENDCOLOUR2:"30 min average"

#day
rrdtool graph $RRDPATH/rrdgraphs/day.png --upper-limit 30 --lower-limit 20 --rigid --start -1d \
DEF:temp1=$RRDPATH/rPItemp.rrd:temp1:AVERAGE \
DEF:temp2=$RRDPATH/rPItemp.rrd:temp2:AVERAGE \
CDEF:trend1=temp1,21600,TREND \
CDEF:trend2=temp2,21600,TREND \
LINE2:temp1$RAWCOLOUR:"Daily Raspberry PI temperature - 1" \
LINE1:trend1$TRENDCOLOUR:"6h average" \
LINE2:temp2$RAWCOLOUR2:"Daily Raspberry PI temperature - 2" \
LINE1:trend2$TRENDCOLOUR2:"6h average"

#week
rrdtool graph $RRDPATH/rrdgraphs/week.png --start -1w \
DEF:temp1=$RRDPATH/rPItemp.rrd:temp1:AVERAGE \
DEF:temp2=$RRDPATH/rPItemp.rrd:temp2:AVERAGE \
LINE2:temp1$RAWCOLOUR:"Weekly Raspberry PI temperature - 1" \
LINE2:temp2$RAWCOLOUR2:"Weekly Raspberry PI temperature - 2" 

#month
rrdtool graph $RRDPATH/rrdgraphs/month.png --start -1m \
DEF:temp1=$RRDPATH/rPItemp.rrd:temp1:AVERAGE \
DEF:temp2=$RRDPATH/rPItemp.rrd:temp2:AVERAGE \
LINE1:temp1$RAWCOLOUR:"Monthly Raspberry PI temperature - 1" \
LINE1:temp2$RAWCOLOUR2:"Monthly Raspberry PI temperature - 2" 

#year
rrdtool graph $RRDPATH/rrdgraphs/year.png --start -1y \
DEF:temp1=$RRDPATH/rPItemp.rrd:temp1:AVERAGE \
DEF:temp2=$RRDPATH/rPItemp.rrd:temp2:AVERAGE \
LINE1:temp1$RAWCOLOUR:"Yearly Raspberry PI temperature - 1" \
LINE1:temp1$RAWCOLOUR2:"Yearly Raspberry PI temperature - 2" 
