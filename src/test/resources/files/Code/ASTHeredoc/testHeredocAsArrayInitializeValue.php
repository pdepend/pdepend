<?php
class Twig_Tests_Node_DebugTest extends Twig_Tests_Node_TestCase
{
    public function getTests()
    {
        $tests = array();

        $tests[] = array(new Twig_Extensions_Node_Debug(null, 0), <<<EOF
if (\$this->env->isDebug()) {
    \$vars = array();
    foreach (\$context as \$key => \$value) {
        if (!\$value instanceof Twig_Template) {
            \$vars[\$key] = \$value;
        }
    }
    var_dump(\$vars);
}
EOF
        );

        $expr = new Twig_Node_Expression_Name('foo', 0);
        $node = new Twig_Extensions_Node_Debug($expr, 0);

        $tests[] = array($node, <<<EOF
if (\$this->env->isDebug()) {
    var_dump((isset(\$context['foo']) ? \$context['foo'] : null));
}
EOF
        );

        return $tests;
    }
}
