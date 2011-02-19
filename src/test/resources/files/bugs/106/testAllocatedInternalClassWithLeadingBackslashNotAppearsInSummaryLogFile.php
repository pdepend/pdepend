<?php
/**
 * @package foo
 */
class testAllocatedInternalClassWithLeadingBackslashNotAppearsInSummaryLogFile
{
    public function testAllocatedInternalClassWithLeadingBackslashNotAppearsInSummaryLogFile()
    {
        throw new \RuntimeException();
    }
}