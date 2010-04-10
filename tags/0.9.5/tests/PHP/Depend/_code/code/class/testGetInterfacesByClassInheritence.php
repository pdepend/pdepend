<?php
namespace bar;
class C implements \foo\B {}

namespace foo;
interface A {}
interface B extends A {}
?>
