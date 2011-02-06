<?php
/**
 * This is one comment.
 */
function func_with_comment()
{
    /**
     * Invalid doc comment location.
     */
}

function func_without_comment()
{
    // A 
    // multiline 
    // inline 
    // comment
}

// Wrong comment type
function func_without_doc_comment()
{
}

/**
 * This is a second comment.
 */
function another_func_with_comment()
{
    // Simple inline comment
}