<?php
function testForeachStatementWithCommentBeforeClosingParenthesis(array $array)
{
    $k = 'key';
    $v = 'value';
    foreach ($array as $k => &$v/* All by ref */) {
    }
    var_dump($k, $y);
}
testForeachStatementWithCommentBeforeClosingParenthesis(array(1, 2, 3, 4));