<?php
function testParserThrowsExceptionForUnclosedSwitchLabelBody()
{
    switch ($foo) {
        case 42:
            break;
