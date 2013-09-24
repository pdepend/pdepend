<?php
class testTraitAdaptation
{
    use testTraitAdaptationOne,
        testTraitAdaptationTwo {

        testTraitAdaptationOne::foo as foo;
        bar as baz;
    }

    private $foo;

    private $bar;
}
