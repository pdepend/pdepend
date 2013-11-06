<?php
/**
 * FANOUT := 3
 * CALLS  := 0
 *
 * @package default
 * @subpackage package
 */

/**
 * Simple test comment.
 *
 * FANOUT := 3
 * CALLS  := 0
 *
 * @package default
 * @subpackage package
 */
class MyPropertyCouplingClass
{
    /**
     * Simple test comment.
     *
     * @var SplObjectStorage
     */
    private $_objects = null;

    /**
     * Simple test comment.
     *
     * @var array(integer => MyObjectItem)
     */
    protected $items = array();

    /**
     * Simple test comment.
     *
     * @var Iterator
     */
    private $_iterator = null;

    /**
     * Simple test comment.
     *
     * @var integer
     */
    public $index = 0;
}
