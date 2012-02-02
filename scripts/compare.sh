#!/bin/sh

rm -rf ~/.pdepend/*
pdepend --summary-xml=/tmp/sum01.xml $1
pdepend --summary-xml=/tmp/sum01.xml $1
src/bin/pdepend.php --summary-xml=/tmp/sum02.xml $1
src/bin/pdepend.php --summary-xml=/tmp/sum03.xml $1
meld /tmp/sum01.xml /tmp/sum02.xml /tmp/sum03.xml 
