<?php
class testGetNodeMetricsReturnsExpectedCboForChildTypeReference_factory
{
    public static function create($type)
    {
        switch ($type) {
            case 'foo':
                return new testGetNodeMetricsReturnsExpectedCboForChildTypeReference_foo();

            case 'bar':
                return new testGetNodeMetricsReturnsExpectedCboForChildTypeReference_bar();

            case 'baz':
                return new testGetNodeMetricsReturnsExpectedCboForChildTypeReference_baz();

            default:
                return new stdClass;
        }
    }
}

class testGetNodeMetricsReturnsExpectedCboForChildTypeReference_foo
    extends testGetNodeMetricsReturnsExpectedCboForChildTypeReference_factory
{

}

class testGetNodeMetricsReturnsExpectedCboForChildTypeReference_bar
    extends testGetNodeMetricsReturnsExpectedCboForChildTypeReference_factory
{

}