<?php
class testTraitUseStatementWithQualifiedAliasHasExpectedEndColumn
{
    use testTraitUseStatementWithQualifiedAliasHasExpectedEndColumnMyTraitOne {
        testTraitUseStatementWithQualifiedAliasHasExpectedEndColumnMyTraitOne::foo
            as bar; }
}
