<?php

namespace PDepend\Bugs;

use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\Source\Language\PHP\PHPParserGeneric;
use PDepend\Source\Language\PHP\PHPTokenizerInternal;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;

/**
 * Test case for bug #713.
 *
 * @ticket 713
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser::parseIssetExpression
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser::parseVariableList
 * @group regressiontest
 */
class ParserBug713Test extends AbstractRegressionTestCase
{
    /**
     * Expect no uncaught exceptions.
     * @doesNotPerformAssertions
     */
    public function testConstantArrayIndexIsset(): void
    {
        $cache = new MemoryCacheDriver();
        $builder = new PHPBuilder();

        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile($this->createCodeResourceURI('bugs/713/testConstantArrayIndexIsset.php'));

        $parser = new PHPParserGeneric($tokenizer, $builder, $cache);
        $parser->parse();
    }
}
