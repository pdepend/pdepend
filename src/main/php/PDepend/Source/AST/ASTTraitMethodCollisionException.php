<?php

/**
 * This file is part of PDepend.
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 1.0.0
 */

namespace PDepend\Source\AST;

use RuntimeException;

/**
 * This type of exception will be thrown when a trait related method collision
 * occurred.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 1.0.0
 */
class ASTTraitMethodCollisionException extends RuntimeException
{
    /**
     * Constructs a new exception instance.
     */
    public function __construct(ASTMethod $method, AbstractASTType $type)
    {
        parent::__construct(
            sprintf(
                'Trait method %s has not been applied, because there are ' .
                'collisions with other trait methods on %s\%s.',
                $method->getImage(),
                preg_replace('(\W+)', '\\', $type->getNamespace()->getImage()),
                $type->getImage(),
            ),
        );
    }
}
