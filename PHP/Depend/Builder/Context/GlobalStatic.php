<?php
class PHP_Depend_Builder_Context_GlobalStatic implements PHP_Depend_Builder_Context
{
    /**
     * The currently used ast builder.
     *
     * @var PHP_Depend_BuilderI
     */
    protected static $builder = null;

    /**
     * Constructs a new builder context instance.
     *
     * @param PHP_Depend_BuilderI $builder The currently used ast builder.
     */
    public function __construct(PHP_Depend_BuilderI $builder)
    {
        self::$builder = $builder;
    }

    /**
     * Returns the class instance for the given qualified name.
     *
     * @param string $qualifiedName Full qualified class name.
     *
     * @return PHP_Depend_Code_Class
     */
    public function getClass($qualifiedName)
    {
        return $this->getBuilder()->getClass($qualifiedName);
    }

    /**
     * Returns a class or an interface instance for the given qualified name.
     *
     * @param string $qualifiedName Full qualified class or interface name.
     *
     * @return PHP_Depend_Code_AbstractClassOrInterface
     */
    public function getClassOrInterface($qualifiedName)
    {
        return $this->getBuilder()->getClassOrInterface($qualifiedName);
    }

    /**
     * Returns the currently used builder instance.
     *
     * @return PHP_Depend_BuilderI
     */
    protected function getBuilder()
    {
        return self::$builder;
    }
}