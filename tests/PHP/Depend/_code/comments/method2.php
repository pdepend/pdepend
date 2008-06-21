<?php
/**
 * A file comment.
 */

/**
 * A simple interface comment.
 */
interface MyInterface
{
    /**
     * This is one comment.
     */
    function method_with_comment();

    function method_without_comment(
    );
    // Wrong comment type
    function method_without_doc_comment();

    /**
     * This is a second comment.
     */
    function another_method_with_comment();
}