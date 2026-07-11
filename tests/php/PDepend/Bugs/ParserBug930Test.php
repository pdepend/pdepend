<?php

namespace PDepend\Bugs;

use PDepend\Source\AST\ASTCastExpression;
use PDepend\Source\AST\ASTInstanceOfExpression;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Ticket;

#[Ticket('930')]
#[Group('regressiontest')]
class ParserBug930Test extends AbstractRegressionTestCase
{
    public function testParserHandlesCastExpressionAfterInstanceOf(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $instanceOf = $function->getFirstChildOfType(ASTInstanceOfExpression::class);

        static::assertInstanceOf(ASTInstanceOfExpression::class, $instanceOf);
        static::assertSame(
            '(string)',
            $instanceOf->getFirstChildOfType(ASTCastExpression::class)?->getImage(),
        );
        static::assertCount(0, $function->getDependencies());
    }
}
