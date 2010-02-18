<?php
namespace bar;
class A implements \foo\E, \foo\C {}

namespace foo;
interface A {}
interface F {}
interface C extends A {}
interface E extends F {}
?>
