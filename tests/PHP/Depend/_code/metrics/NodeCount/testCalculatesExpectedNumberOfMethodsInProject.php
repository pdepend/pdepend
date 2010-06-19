<?php
namespace A {
    class A1 {
        function a1a() {}
        function a1b() {}
    }
    class A2 {
        function a2a() {}
    }
    interface A3 {
        function a3a();
    }
}

namespace B {
    interface B1 {
        function b1a();
        function b2a();
    }
    interface B2 {
        function b2a();
    }
}

namespace C {
    interface C1 {
        function c1a();
        function c2a();
    }
}