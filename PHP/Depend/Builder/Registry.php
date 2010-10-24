<?php
class PHP_Depend_Builder_Registry
{
    private static $_default = null;

    public static function setDefault(PHP_Depend_BuilderI $builder)
    {
        self::$_default = $builder;
    }

    /**
     *
     * @return PHP_Depend_BuilderI
     */
    public static function getDefault()
    {
        if (self::$_default === null) {
            throw new RuntimeException('ERROR');
        }
        return self::$_default;
    }
}