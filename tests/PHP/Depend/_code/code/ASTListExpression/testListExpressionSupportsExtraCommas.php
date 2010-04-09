<?php
function testListExpressionSupportsExtraCommas()
{
    list(, , ,, $a, $b, ,, $c , ,,) = array("a", "b", "c");
}
