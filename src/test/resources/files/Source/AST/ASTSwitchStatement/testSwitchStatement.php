<?php
function testSwitchStatement( $x, $y, $z )
{
    switch ($x && $y || $z) {
        case true: break;
        case false: break;
        default: break;
    }
}
