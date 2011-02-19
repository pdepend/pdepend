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
     * @var SplObjectStorage $_objects
     */
    private $_objects = null;

    /**
     * Simple test comment.
     *
     * @var array(integer => MyObjectItem) $items
     */
    protected $items = array();

    /**
     * Simple test comment.
     *
     * @var Iterator $_iterator
     */
    private $_iterator = null;

    /**
     * Simple test comment.
     *
     * @var integer $index
     */
    public $index = 0;
}