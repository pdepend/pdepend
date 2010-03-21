<?php
/**
 * @package package
 */
class Clazz {
	function method()	{
		$this->charset = $GLOBALS['LANG']->charSet ? $GLOBALS['LANG']->charSet : $this->charset;
	}
}
