<?php
function testSwitchDefaultLabelCanBeTerminatedWithSemicolon()
{
    switch ($foo) {
        default;
            return $foo;
    }
}