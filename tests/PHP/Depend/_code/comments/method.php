<?php
/**
 * A simple class comment.
 */
class MyMethodClass
{
    /**
     * This is one comment.
     */
    function method_with_comment()
    {
        /**
         * Invalid doc comment location.
         */
    }

    function method_without_comment()
    {
    }

    // Wrong comment type
    function method_without_doc_comment()
    {
    }

    /**
     * This is a second comment.
     */
    function another_method_with_comment()
    {
        // A test comment.
        // Another test comment.
    }
}