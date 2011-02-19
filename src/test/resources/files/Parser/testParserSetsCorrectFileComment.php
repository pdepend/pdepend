<?php
/**
 * FANOUT := 12
 * CALLS  := 10
 *
 * @package default
 * @subpackage package
 */

/**
 * Simple test comment.
 *
 * FANOUT := 2
 * CALLS  := 0
 * 
 * @package default
 * @subpackage package
 */
interface MyCouplingInterface
{
    /**
     * 
     * Simple test comment.
     * 
     * FANOUT := 2
     * CALLS  := 0
     * 
     * @param ArrayAccess $items The input items.
     * @param integer     $index The requested index.
     * 
     * @return void
     * @throws OutOfRangeException For invalid index values.
     */
    function removeItemAt(ArrayObject $items, $index);
}

/**
 * Simple test comment.
 *
 * FANOUT := 10
 * CALLS  := 10
 * 
 * @package default
 * @subpackage package
 */
class MyCouplingClass
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

    /**
     * 
     * Simple test comment.
     * 
     * FANOUT := 3
     * CALLS  := 3
     * 
     * @param ArrayAccess $items The input items.
     * @param integer     $index The requested index.
     * 
     * @return void
     * @throws OutOfRangeException For invalid index values.
     */
    public function removeItemAt(ArrayObject $items, $index)
    {
        if (is_int($index) === false) {
            throw new InvalidArgumentException('Error');
        }
        if (!$items->offsetExists($index)) {
            throw new OutOfRangeException('Error...');
        }
        $items->offsetUnset($index);
    }
    
    /**
     * Simple test comment.
     * 
     * FANOUT := 4
     * CALLS  := 7
     * 
     * @param ArrayAccess $items The input items.
     * @param integer     $index The requested index.
     * 
     * @return MyObjectItem
     * @throws OutOfRangeException For invalid index values.
     * @throws InvalidArgumentException For invalid index values.
     */
    public function getItemAt(ArrayAccess $items, $index)
    {
        if (is_int($index) === false) {
            throw new InvalidArgumentException('Error');
        }
        if (!$items->offsetExists($index)) {
            throw new OutOfRangeException('Error...');
        }
        $data = $items->offsetGet($index);
        if (is_array($data)) {
            return new MyObjectItem(array_keys($data), array_values($data));
        }
        return MyObjectItem::getDefault();
    }
}
