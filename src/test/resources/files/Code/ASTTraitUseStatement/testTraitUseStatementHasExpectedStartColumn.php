<?php
class testTraitUseStatementHasExpectedStartColumn
{
    use
        /* ... */ MyTraitOne /* ... */,
        MyTraitTwo/* ... */,
        // ...
        MyTraitThree /* ... */
            ;
}
