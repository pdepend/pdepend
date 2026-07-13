<?php

namespace PDepend\Bugs;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Ticket;

#[Ticket('929')]
#[Group('regressiontest')]
class ParserBug929Test extends AbstractRegressionTestCase
{
    public function testParserHandlesNamespacePrefixedScalarDefaults(): void
    {
        $parameters = $this->getFirstMethodForTestCase()->getParameters();

        static::assertFalse($parameters[0]->getDefaultValue());
        static::assertTrue($parameters[1]->getDefaultValue());
        static::assertNull($parameters[2]->getDefaultValue());
    }
}
