<?php
class testParserHandlesBacktickExpressionWithEmbeddedStringExpression
{
    public function foo()
    {
        return `Manuel Pichler "$ticketNo"`;
    }
}