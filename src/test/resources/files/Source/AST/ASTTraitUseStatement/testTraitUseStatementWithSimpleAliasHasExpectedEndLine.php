<?php
class testTraitUseStatementWithSimpleAliasHasExpectedEndLine
{
    use testTraitUseStatementWithSimpleAliasHasExpectedEndLineMyTraitOne {
        foo as bar;
    }
}
