<?php

namespace PDepend\Metrics;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AnalyzerFactory
{
    private $container;
    private $options;

    public function __construct(ContainerInterface $container, array $options = array())
    {
        $this->container = $container;
        $this->options = $options;
    }

    /**
     * Create and configure all analyzers required for given set of loggers.
     *
     * @param array $generators
     * @return array(PDepend\Metrics\AbstractAnalyzer)
     */
    public function createRequiredForGenerators(array $generators)
    {
        $analyzers = array();

        foreach ($generators as $logger) {
            foreach ($logger->getAcceptedAnalyzers() as $type) {
                $analyzers[$type] = $this->container->get($type);
            }
        }

        //$cacheKey = md5(serialize($this->files) . serialize($this->directories));
        $cacheKey = 'pdepend';

        $cache = $this->cacheFactory->create($cacheKey);

        foreach ($analyzers as $analyzer) {
            if ($analyzer instanceof AnalyzerCacheAware) {
                $analyzer->setCache($cache);
            }
        }

        return $analyzers;
    }
}
