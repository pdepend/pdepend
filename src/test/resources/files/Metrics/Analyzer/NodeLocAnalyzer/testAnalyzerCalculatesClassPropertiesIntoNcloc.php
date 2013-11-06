<?php
/**
 * A simple class comment.
 */
class MyPropertyClass
{
    /**
     * This is one comment.
     */
    private $property_with_comment;

    // Wrong comment type
    public $property_without_doc_comment;

    protected $property_without_comment = null;

    /**
     * This is a second comment.
     */
    protected $another_property_with_comment;
}