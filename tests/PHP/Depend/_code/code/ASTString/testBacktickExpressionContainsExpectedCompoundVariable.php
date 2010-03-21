<?php
function testBacktickExpressionContainsExpectedCompoundVariable() {
    $expr = `${$foo * $bar}`;
}