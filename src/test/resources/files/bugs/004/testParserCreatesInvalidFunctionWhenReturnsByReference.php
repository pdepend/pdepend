<?php
/**
 * The parser implementation didn't handle reference return values correct.
 *
 * http://bugs.xplib.de/index.php?do=details&task_id=8&project=3
 *
 * @package package0
 */
class clazz0
{
    public function &fooBug08()
    {
    }
}

/**
 * @package package0
 */
function &barBug08()
{

}