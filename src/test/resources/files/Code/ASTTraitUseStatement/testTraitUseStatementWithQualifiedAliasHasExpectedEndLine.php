<?php
class testTraitUseStatementWithQualifiedAliasHasExpectedEndLine
{
    use testTraitUseStatementWithQualifiedAliasHasExpectedEndLineMyTraitOne {
        foo as bar;
    }
}
