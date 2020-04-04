<?php
trait A {
    function test() {
        echo 'a';
    }
}

trait B {
    use A;
}

class C {
    use A;
    use B {
        A::test insteadof B;
    }
}
