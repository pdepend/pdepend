<?php

class Foo implements I {
    use T;

    const string|int TEST = E::TEST;  // Foo::TEST must be a string or int
}

class Bar extends Foo {
    const string TEST = "Test2";  // Bar::TEST must be a string, but the value can change
}
