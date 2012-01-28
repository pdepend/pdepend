<?php
class testTraitAdaptationHasExpectedStartLine
{
    use testTraitAdaptationHasExpectedStartLineOne,
        testTraitAdaptationHasExpectedStartLineTwo {

        testTraitAdaptationHasExpectedStartLineOne::foo as foo;
        bar as baz;
    }

    private $foo;

    private $bar;
}
