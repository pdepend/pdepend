<?php
function testTokenizerDoesNotDetectNowdocSyntaxInString()
{
    $data = "<<<<<<<<<STDERR<<<<<<<<<<<";
    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
XML;
}