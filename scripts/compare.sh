#!/bin/sh

rm -rf ~/.pdepend/*
pdepend --jdepend-xml=/tmp/jdpa0.xml --summary-xml=/tmp/suma0.xml $1
pdepend --jdepend-xml=/tmp/jdpa1.xml --summary-xml=/tmp/suma1.xml $1
src/bin/pdepend.php --jdepend-xml=/tmp/jdpb0.xml --summary-xml=/tmp/sumb0.xml $1
src/bin/pdepend.php --jdepend-xml=/tmp/jdpb1.xml --summary-xml=/tmp/sumb1.xml $1

meld /tmp/suma0.xml /tmp/sumb0.xml /tmp/sumb1.xml

meld /tmp/jdpa0.xml /tmp/jdpb0.xml /tmp/jdpb1.xml
