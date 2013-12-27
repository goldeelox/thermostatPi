ThermostatPi


Set Up
------
Add to rc.local:
sudo modprobe w1-gpio
sudo modprobe w1-therm

Find the device:
$ cat /sys/bus/w1/devices/w1_bus_master1/w1_master_slaves
Result: 28-0000032837ea
Now cat the device:
$ cat /sys/bus/w1/devices/28-0000032837ea/w1_slave
Save this path in config.inc.php

Install RRD:
sudo apt-get install rrdtool

Set up RRD DB:
$ rrdtool create rPItemp.rrd --step 300 \
DS:temp1:GAUGE:600:-30:50 \
DS:temp2:GAUGE:600:-30:50 \
RRA:AVERAGE:0.5:1:12 \
RRA:AVERAGE:0.5:1:288 \
RRA:AVERAGE:0.5:12:168 \
RRA:AVERAGE:0.5:12:720 \
RRA:AVERAGE:0.5:288:365

Crontab
* * * * * /usr/bin/php /home/USERPATH/thermostatpi/get_temp_rrd.php >> /tmp/tempcron.txt
* * * * * /bin/sh /home/USERPATH/thermostatpi/generate_rrd_graphs.sh



*** Update USB IR Toy Firmware ***
Help URLS: 
 - http://dangerousprototypes.com/docs/USB_IR_Toy_firmware_update
 - http://www.diolan.com/pic/bootloader_dwn.html

Instructions:
Short pins PGC and PGD
wget http://jesshaas.com/software/IRToy-fw_update.tar.gz
export LIBS=-lusb
sudo apt-get install libusb-dev
sudo ./configure (I got g++ errors if I run as pi)
sudo make
cd ..
sudo IRToy-fw_update/fw_update -e -w -v -m all -vid 0x04D8 -pid 0xFD0B -ix USBIRToy.package.v22/firmware/USBIRToy.v22.hex


IR Toy:
Get library phpIRToy.inc.php from https://bitbucket.org/cdrum/phpirtoy and place in lib folder.


Add apache's user to the dialout group in /etc/group
add "extension=dio.so" to php.ini

sudo apt-get install php5-sqlite

mkdir log
chmod 770 log
chgrp www-data log
mkdir db
chmod 770 db
chgrp www-data db


Record presets using ir_store_codes.php

