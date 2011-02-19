<?php
class testConstantDefinitionHasExpectedDocCommentWithInlineCommentBetween
{
    /**
     * Foo bar baz foobar.
     */
    /* Test Comment */
    const FOO = 42;
}