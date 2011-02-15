<?php
/**
 * FANOUT := 9
 * CALLS  := 10
 *
 * @package default
 * @subpackage package
 */

/**
 * Simple test comment.
 *
 * FANOUT := 7
 * CALLS  := 10
 *
 * @package default
 * @subpackage package
 */
class MyMethodCouplingClass
{
    /**
     * Simple test comment.
     *
     * FANOUT := 4
     * CALLS  := 7
     *
     * @param ArrayAccess $items The input items.
     * @param integer     $index The requested index.
     *
     * @return MyObject
     * @throws OutOfBoundsException For invalid index values.
     * @throws InvalidArgumentException For invalid index values.
     */
    public function getItemAt(ArrayAccess $items, $index)
    {
        if (is_int($index) === false) {
            throw new InvalidArgumentException('Error');
        }
        if (!$items->offsetExists($index)) {
            throw new OutOfBoundsException('Error...');
        }
        $data = $items->offsetGet($index);
        if (is_array($data)) {
            return new MyObjectItem(array_keys($data), array_values($data));
        }
        return MyObjectItem::getDefault();
    }

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
}


/**
 * Simple test comment.
 *
 * FANOUT := 2
 * CALLS  := 0
 *
 * @package default
 * @subpackage package
 */
interface MyMethodCouplingInterface
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