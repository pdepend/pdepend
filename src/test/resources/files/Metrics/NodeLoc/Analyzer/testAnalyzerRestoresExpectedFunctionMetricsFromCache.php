<?php
function testAnalyzerRestoresExpectedFunctionMetricsFromCache($foo)
{
    switch ($foo)
    {
        case 1:
            foreach (array() as $x) {

            }
            break;

        case 17:
            for ($i = 0; $i < 23; ++$i)
            {
                echo $i, PHP_EOL;
            }
            break;

        case 23:
            if ( time() % 23 )
            {
                echo "YES";
            }
            else
            {
                echo "NO";
            }
            break;

        case 42:
            throw new Exception();
    }

    if (true)
    {
        return 42;
    }
    return 23;
}
