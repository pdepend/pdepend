<?php

namespace PDepend\Metrics;

class AnalyzerFactory
{
    private $container;

    public function create($identifier)
    {
        return $this->container->get($identifier);
    }
}
