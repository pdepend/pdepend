<?php
class testGetNodeMetricsReturnsExpectedCaForCountsSameTypeOnlyOnce extends Exception
{

}

/**
 * @throws testGetNodeMetricsReturnsExpectedCaForCountsSameTypeOnlyOnce
 * @param testGetNodeMetricsReturnsExpectedCaForCountsSameTypeOnlyOnce $o
 * @return testGetNodeMetricsReturnsExpectedCaForCountsSameTypeOnlyOnce
 */
function foo(testGetNodeMetricsReturnsExpectedCaForCountsSameTypeOnlyOnce $o)
{
    if ($o instanceof testGetNodeMetricsReturnsExpectedCaForCountsSameTypeOnlyOnce) {
        throw new testGetNodeMetricsReturnsExpectedCaForCountsSameTypeOnlyOnce();
    }
    return new testGetNodeMetricsReturnsExpectedCaForCountsSameTypeOnlyOnce();
}

/**
 * @return void
 * @throws testGetNodeMetricsReturnsExpectedCaForCountsSameTypeOnlyOnce
 */
function bar(testGetNodeMetricsReturnsExpectedCaForCountsSameTypeOnlyOnce $o)
{
    if ($o instanceof testGetNodeMetricsReturnsExpectedCaForCountsSameTypeOnlyOnce) {
        throw new testGetNodeMetricsReturnsExpectedCaForCountsSameTypeOnlyOnce();
    }
}