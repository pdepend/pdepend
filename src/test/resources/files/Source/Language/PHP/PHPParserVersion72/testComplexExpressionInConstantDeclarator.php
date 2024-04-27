<?php
class testComplexExpressionInConstantDeclarator {
    // none of these work
    const A = 1 ^ 2;
    const B = 1 % 2;
    const C = ~4;
    const D = (1 + 2);
    //const E = {"".""};
    const E = 1 ? 42 : 23;
    const F = 1 === 2;
    const G = 1 << 42;
    const H = 1 >> 42;
    //const I = @42;
    //const J = (bool) 2;
    //const K = 2++;
    const K = <<<X
xxx
X;

    const BAR = 1+2-3*4/5;
    const BAZ = 'hello ' . 'world';
}
