<?php
function testReturnsByReferenceReturnsFalseByDefault()
{
    array_map(function() {
        return null;
    }, array());
}