<?php
class testGetNodeMetricsReturnsExpectedCaForChildTypeReference_factory
{
    public static function create($type)
    {
        switch ($type) {
            case 'foo':
                return new testGetNodeMetricsReturnsExpectedCaForChildTypeReference_foo();

            case 'bar':
                return new testGetNodeMetricsReturnsExpectedCaForChildTypeReference_bar();

            case 'baz':
                return new testGetNodeMetricsReturnsExpectedCaForChildTypeReference_baz();

            default:
                return new stdClass;
        }
    }
}

class testGetNodeMetricsReturnsExpectedCaForChildTypeReference_foo
    extends testGetNodeMetricsReturnsExpectedCaForChildTypeReference_factory
{
    public function setFactory(testGetNodeMetricsReturnsExpectedCaForChildTypeReference_factory $factory)
    {

    }
}

class testGetNodeMetricsReturnsExpectedCaForChildTypeReference_bar
{
    public function setFactory(testGetNodeMetricsReturnsExpectedCaForChildTypeReference_factory $factory)
    {

    }
}

class testGetNodeMetricsReturnsExpectedCaForChildTypeReference_baz
{
    public function setFactory(testGetNodeMetricsReturnsExpectedCaForChildTypeReference_factory $factory)
    {

    }
}