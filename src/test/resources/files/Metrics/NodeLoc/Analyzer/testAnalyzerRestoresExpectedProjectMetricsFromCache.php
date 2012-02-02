<?php
class testAnalyzerReturnsExpectedProjectMetricsFromCacheClass
{
    public $foo;

    protected function bar()
    {

    }

    private  function baz()
    {
        if (true)
        {
            return 42;
        }
        return 23;
    }
}

class testAnalyzerReturnsExpectedProjectMetricsFromCacheMethod
{
    public $foo;

    private  function baz()
    {
        switch ($this->foo)
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
}

function testAnalyzerRestoresExpectedProjectMetricsFromCacheFunction($foo)
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

interface testAnalyzerRestoresExpectedProjectMetricsFromCacheInterface
{
    const FOO = 23;

    public function bar();

    public function baz();
}
