<?php
class testTraitAdaptationHasExpectedEndColumn
{
    use testTraitAdaptationHasExpectedEndColumnOne,
        testTraitAdaptationHasExpectedEndColumnTwo {

        testTraitAdaptationHasExpectedEndColumnOne::foo as foo;
        bar as baz;
    }

    private $foo;

    private $bar;
}
