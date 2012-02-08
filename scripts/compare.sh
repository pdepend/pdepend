#!/bin/sh

rm -rf ~/.pdepend/*
pdepend --jdepend-xml=/tmp/jdp01.xml --summary-xml=/tmp/sum01.xml $1
pdepend --jdepend-xml=/tmp/jdp01c.xml --summary-xml=/tmp/sum01c.xml $1
src/bin/pdepend.php --jdepend-xml=/tmp/jdp02.xml --summary-xml=/tmp/sum02.xml $1
src/bin/pdepend.php --jdepend-xml=/tmp/jdp03.xml --summary-xml=/tmp/sum03.xml $1

meld /tmp/sum01.xml /tmp/sum02.xml /tmp/sum03.xml

meld /tmp/jdp01.xml /tmp/jdp02.xml /tmp/jdp03.xml
