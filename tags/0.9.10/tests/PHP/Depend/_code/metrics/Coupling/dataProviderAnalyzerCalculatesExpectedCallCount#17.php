<?php
/**
 * @package test
 */
function dataProviderAnalyzerCalculatesExpectedCallCount17()
{
    foo::bar()->bar();
    f00::bar()->bar();
}