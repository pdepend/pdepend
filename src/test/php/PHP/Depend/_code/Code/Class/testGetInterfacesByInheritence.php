<?php
namespace bar;
class B implements \foo\E, \foo\C {}
class A extends B implements \foo\D, \foo\A {}

namespace foo;
interface A {}
interface F {}
interface B extends A {}
interface C extends A {}
interface E extends F {}
interface D extends B, E {}
?>
