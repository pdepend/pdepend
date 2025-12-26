<?php
function testAnonymousClassSupportsProperties()
{
    $o = new class() {
        public int $a = 0;

        public function sum(): int
        {
            return $this->a++;
        }
    };
}
