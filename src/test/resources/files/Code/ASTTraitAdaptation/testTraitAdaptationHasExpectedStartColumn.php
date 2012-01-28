<?php
class testTraitAdaptationHasExpectedStartColumn
{
    use testTraitAdaptationHasExpectedStartColumnOne,
        testTraitAdaptationHasExpectedStartColumnTwo {

        testTraitAdaptationHasExpectedStartColumnOne::foo as foo;
        bar as baz;
    }

    private $foo;

    private $bar;
}
