<?php
function testNPathComplexityForComplexNestedControlStatements()
{
    if ($obj->p0) {
        // owning side MUST have a join table
        if ( ! isset($v1['jt']) || ! $v1['jt']) {
            // Apply default join table
            $v2 = substr($obj->p1, strrpos($obj->p1, '\\') + 1);
            $v3 = substr($obj->p2, strrpos($obj->p2, '\\') + 1);
            $v1['jt'] = array(
            );
            $obj->jt = $v1['jt'];
        }
        // owning side MUST specify jc
        else if ( ! isset($v1['jt']['jc'])) {
            throw new Exception($obj->p1, 'jc', 'txt / plural s?');
        }
        // owning side MUST specify ijc
        else if ( ! isset($v1['jt']['ijc'])) {
            throw new Exception($obj->p2, 'ijc', 'txt / plural s?');
        }

        foreach ($v1['jt']['jc'] as $v4) {
            $obj->p3[$v4['n']] = $v4['rcn'];
            $obj->p4[] = $v4['n'];
        }

        foreach ($v1['jt']['ijc'] as $ijc) {
            $obj->p5[$ijc['n']] = $ijc['rc'];
            $obj->p4[] = $ijc['n'];
        }
    }

    if (isset($v1['ob'])) {
        if ( ! is_array($v1['ob'])) {
            throw new InvalidArgumentException("'ob' ".gettype($v1['ob']));
        }
        $obj->ob = $v1['ob'];
    }
}