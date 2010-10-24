<?php
function testIfStatementAlternativeScopeHasExpectedEndLine()
{
    if (true) /*
               * Comment
               */
        :
        echo 'True', PHP_EOL;
    endif // ...
        ;
}

testIfStatementAlternativeScopeHasExpectedEndLine();