<?php
class testTraitUseStatementHasExpectedStartLine
{
    use
        /* ... */ MyTraitOne /* ... */,
        MyTraitTwo/* ... */,
        // ...
        MyTraitThree /* ... */
            ;
}
