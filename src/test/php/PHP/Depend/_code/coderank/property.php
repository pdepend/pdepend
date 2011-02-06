<?php
/**
 * Class comment.
 * 
 * @package PDepend::CodeRankA
 */
class PDepend_CodeRank_ClassA
{
    /**
     * Property comment.
     *
     * @var PDepend_CodeRank_ClassA
     */
    protected $classA = null;
    
    /**
     * Property comment.
     *
     * @var PDepend_CodeRank_ClassB
     */
    protected $classB = null;
    
    /**
     * Property comment.
     *
     * @var PDepend_CodeRank_ClassC
     */
    protected $classC = null;
}

/**
 * Class comment.
 * 
 * @package PDepend::CodeRankA
 */
class PDepend_CodeRank_ClassB
{
    /**
     * Property comment.
     *
     * @var PDepend_CodeRank_ClassB
     */
    protected $classB = null;
    
    /**
     * Property comment.
     *
     * @var PDepend_CodeRank_ClassC
     */
    protected $classC1 = null;
    
    /**
     * Property comment.
     *
     * @var PDepend_CodeRank_ClassC
     */
    protected $classC2 = null;
}

/**
 * Class comment.
 * 
 * @package PDepend::CodeRankB
 */
class PDepend_CodeRank_ClassC
{
    /**
     * Property comment.
     *
     * @var string $_literal
     */
    private $_literal = '';
    
    /**
     * Property comment.
     *
     * @var PDepend_CodeRank_ClassA
     */
    private $_classA = null;
}