<?php
function testSwitchLabelCanBeTerminatedWithSemicolon($foo)
{
    switch ($foo) {
        case true;
            return $foo;
    }
}