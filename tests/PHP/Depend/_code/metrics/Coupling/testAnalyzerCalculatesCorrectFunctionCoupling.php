<?php
/**
 * FANOUT := 7
 * CALLS  := 10
 *
 * @package default
 * @subpackage package
 */

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
function getItemAt(ArrayAccess $items, $index)
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
function removeItemAt(ArrayObject $items, $index)
{
    if (is_int($index) === false) {
        throw new InvalidArgumentException('Error');
    }
    if (!$items->offsetExists($index)) {
        throw new OutOfRangeException('Error...');
    }
    $items->offsetUnset($index);
}