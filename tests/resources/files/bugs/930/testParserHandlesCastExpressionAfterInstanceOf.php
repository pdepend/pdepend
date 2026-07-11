<?php

function testParserHandlesCastExpressionAfterInstanceOf($job, $jobToFake)
{
    if ($job instanceof ((string) $jobToFake)) {
    }
}
