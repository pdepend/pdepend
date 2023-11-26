<?php

class Foo implements I {
    use T;

    const string TEST = E::TEST;  // Foo::TEST must also be a string
}

class Bar extends Foo {
    const string TEST = "Test2";  // Bar::TEST must also be a string, but the value can change
}
