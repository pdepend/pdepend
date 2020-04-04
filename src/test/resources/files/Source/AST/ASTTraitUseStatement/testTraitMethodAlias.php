<?php
trait A {
    function test() {
        echo 'a';
    }
}

trait B {
    function test() {
        echo 'b';
    }
}

class C {
    use A {
        test as testA;
    }
    use B {
        test as testB;
    }

    function test() {
        echo 'c';
    }
}
