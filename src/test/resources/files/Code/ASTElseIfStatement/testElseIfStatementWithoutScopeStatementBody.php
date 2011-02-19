<?php
function testElseIfStatementWithoutScopeStatementBody()
{
    if (time() < 0) {
    
    } elseif (time() > 0)
        foreach (array(1, 2, 3, 4, 5) as $i)
            echo $i;
}

testElseIfStatementWithoutScopeStatementBody();