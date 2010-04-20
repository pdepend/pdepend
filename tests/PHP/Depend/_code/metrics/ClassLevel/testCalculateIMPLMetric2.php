<?php
namespace bar {
    class A implements \foo\C {}
    class B implements \foo\E, \foo\C {}
    class C extends A implements \foo\D, \foo\A {}
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
