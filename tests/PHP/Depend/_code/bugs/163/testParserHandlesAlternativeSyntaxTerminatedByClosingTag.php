<?php
function testParserHandlesAlternativeSyntaxTerminatedByClosingTag($c = 42)
{
    for ($i = 1; $i <=$c; ++$i):
        echo $i, PHP_EOL;
    endfor ?>Total: <?php echo $c, PHP_EOL;
}

testParserHandlesAlternativeSyntaxTerminatedByClosingTag();