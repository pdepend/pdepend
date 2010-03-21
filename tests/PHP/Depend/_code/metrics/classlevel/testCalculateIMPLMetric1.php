<?php
namespace bar {
    class A extends C implements \foo\D, \foo\A {}
    class B implements \foo\E, \foo\C {}
    class C implements \foo\C {}
}

namespace foo {
    interface A {}
    interface B extends A {}
    interface C extends A {}
    interface D extends B, E {}
    interface E extends F {}
    interface F {}
}
?>
