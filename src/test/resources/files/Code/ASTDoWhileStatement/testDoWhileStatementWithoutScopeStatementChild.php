<?php
function testDoWhileStatementWithoutScopeStatementChild()
{
    do
        if (!isset($i)) $i = 0;
    while (++$i < 5);
    
    return $i;
}

echo testDoWhileStatementWithoutScopeStatementChild();