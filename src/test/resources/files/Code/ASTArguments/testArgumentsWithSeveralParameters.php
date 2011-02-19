<?php
function testArgumentsWithSeveralParameters()
{
    bar(
        baz(), // Long help texts,
        null
    );
}