<?php

class Unrelated {}

trait Base {
	function foo() {}
}

trait Derived1 {
	use Base;
}

class Concrete extends Unrelated {
	use Base, Derived1;
}
