<?php
interface IFoo {
}
class Bar {
    function foo($x) {
        return ($x instanceof IFoo);
    }
}
?>
