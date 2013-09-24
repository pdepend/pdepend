<?php
function testIfStatementAlternativeScopeHasExpectedEndColumn()
{
    if (true) /*
               * Comment
               */
        :
        echo 'True', PHP_EOL;
    endif // ...
        ;
}

testIfStatementAlternativeScopeHasExpectedEndColumn();