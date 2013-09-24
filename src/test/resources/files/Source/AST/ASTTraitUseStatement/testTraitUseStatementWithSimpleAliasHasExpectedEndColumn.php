<?php
class testTraitUseStatementWithSimpleAliasHasExpectedEndColumn
{
    use testTraitUseStatementWithSimpleAliasHasExpectedEndColumnMyTraitOne {
        foo as bar; }
}
