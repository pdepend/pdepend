<?php
class testConstantDefinitionWithDeclarators
{
    const FOO /* */ = /* */ 23,
            /* */ BAR = //
                42 /** */
                    ;
}
