<?php
/**
 * Class comment.
 * 
 * @package PDepend::CodeRankA
 */
class PDepend_CodeRank_ClassA
{
    /**
     * Method comment.
     *
     * @return PDepend_CodeRank_ClassA
     */
    public function methodA()
    {
        return $this;
    }
    
    /**
     * Method comment.
     *
     * @param PDepend_CodeRank_ClassB $classB Instance
     * 
     * @return void
     */
    protected function methodB(PDepend_CodeRank_ClassB $classB)
    {
    }
    
    /**
     * Method comment.
     *
     * @return void
     * @throws PDepend_CodeRank_ClassC
     */
    private function methodC() {}
}

/**
 * Class comment.
 * 
 * @package PDepend::CodeRankA
 */
class PDepend_CodeRank_ClassB
{
    /**
     * Method comment.
     * 
     * @param PDepend_CodeRank_ClassB $classB Instance
     * 
     * @return void
     */
    public function methodB(PDepend_CodeRank_ClassB $classB) {}
    
    /**
     * Method comment.
     *
     * @return PDepend_CodeRank_ClassC
     * @throws PDepend_CodeRank_ClassC
     */
    public function methodC() {}
}

/**
 * Class comment.
 * 
 * @package PDepend::CodeRankB
 */
class PDepend_CodeRank_ClassC
{
    /**
     * Method comment.
     *
     * @return string
     */
    public function __toString()
    {
        return '';
    }
    
    /**
     * Method comment.
     *
     * @return PDepend_CodeRank_ClassA
     */
    protected function methodA() {}
}