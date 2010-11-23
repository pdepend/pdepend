<?php
class testAnalyzerCountsNumberOfMethodsForClassSize
{
    public $foo = 0;
    protected $bar = 1;
    private $baz = 2;

    public function foo()
    {
        if (func_num_args() === 3) {
            return 0;
        } else if (func_num_args() === 2) {
            return 1;
        } else if (func_num_args() === 1) {
            return 2;
        }
        return 3;
    }

    protected function bar()
    {
        if (func_num_args() === 3) {
            return 0;
        } else if (func_num_args() === 2) {
            return 1;
        } else if (func_num_args() === 1) {
            return 2;
        }
        return 3;
    }

    private function baz()
    {
        if (func_num_args() === 3) {
            return 0;
        } else if (func_num_args() === 2) {
            return 1;
        } else if (func_num_args() === 1) {
            return 2;
        }
        return 3;
    }
}