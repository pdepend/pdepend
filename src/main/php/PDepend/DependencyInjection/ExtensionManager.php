<?php

namespace PDepend\DependencyInjection;

/**
 * Manage activation and registration of extensions for PDepend.
 */
class ExtensionManager
{
    private $extensions = array();

    public function activateExtension($className)
    {
        if (!class_exists($className)) {
            throw new \RuntimeException(
                sprintf(
                    'Cannot find extension class %s" for PDepend. Maybe the plugin is not installed?',
                    $className
                )
            );
        }

        $extension = new $className;

        if (!($extension instanceof Extension)) {
            throw new \RuntimeException(
                sprintf('Class "%s" is not a valid Extension', $className)
            );
        }

        $this->extensions[$extension->getName()] = $extension;
    }

    /**
     * Return all activated extensions.
     *
     * @return array(\PDepend\DependencyInjection\Extension)
     */
    public function getActivatedExtensions()
    {
        return $this->extensions;
    }
}
