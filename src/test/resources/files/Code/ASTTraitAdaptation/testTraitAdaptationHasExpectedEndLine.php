<?php
class testTraitAdaptationHasExpectedEndLine
{
    use testTraitAdaptationHasExpectedEndLineOne,
        testTraitAdaptationHasExpectedEndLineTwo {

        testTraitAdaptationHasExpectedEndLineOne::foo as foo;
        bar as baz;
    }

    private $foo;

    private $bar;
}
