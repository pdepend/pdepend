<?php
namespace library {
    class Q {}
}

namespace system {
    class A {}
    class B extends A {}
    class C extends A {}
    class D extends A {}
    class E extends A {}
    class F extends B {}
    class G extends B {}
    class H extends D {}
    class I extends E {}
    class J extends E {}
    class K extends H {}
    class L extends J {}
    class M extends L {}

    class N {}
    class O extends N {}
    class P extends N {}

    class R extends \library\Q {}

    class S {}

    interface T {}
    class U implements T {}
}
?>
