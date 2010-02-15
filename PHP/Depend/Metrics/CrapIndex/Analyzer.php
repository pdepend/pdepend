<?php

require_once 'PHP/Depend/Metrics/NodeAwareI.php';
require_once 'PHP/Depend/Metrics/AbstractAnalyzer.php';
require_once 'PHP/Depend/Metrics/AggregateAnalyzerI.php';
require_once 'PHP/Depend/Metrics/CyclomaticComplexity/Analyzer.php';
require_once 'PHP/Depend/Util/Coverage/Factory.php';

class PHP_Depend_Metrics_CrapIndex_Analyzer
       extends PHP_Depend_Metrics_AbstractAnalyzer
    implements PHP_Depend_Metrics_AggregateAnalyzerI,
               PHP_Depend_Metrics_NodeAwareI
{

    private $_metrics = array();

    /**
     * The coverage report instance representing the supplied coverage report
     * file.
     *
     * @var PHP_Depend_Util_Coverage_Report
     */
    private $_report = null;

    /**
     *
     * @var PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     */
    private $_ccnAnalyzer = array();

    public function isEnabled()
    {
        return isset($this->options['coverage-report']);
    }

    public function getNodeMetrics(PHP_Depend_Code_NodeI $node)
    {
        if (isset($this->_metrics[$node->getUUID()])) {
            return $this->_metrics[$node->getUUID()];
        }
        return array();
    }

    public function getRequiredAnalyzers()
    {
        return array(PHP_Depend_Metrics_CyclomaticComplexity_Analyzer::CLAZZ);
    }

    public function addAnalyzer(PHP_Depend_Metrics_AnalyzerI $analyzer)
    {
        $this->_ccnAnalyzer = $analyzer;
    }

    public function analyze(PHP_Depend_Code_NodeIterator $packages)
    {
        if ($this->isEnabled()) {
            $this->_analyze($packages);
        }
    }

    private function _analyze(PHP_Depend_Code_NodeIterator $packages)
    {
        $this->_ccnAnalyzer->analyze($packages);

        $this->fireStartAnalyzer();

        foreach ($packages as $package) {
            $package->accept($this);
        }

        $this->fireEndAnalyzer();
    }

    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        if ($method->isAbstract() === false) {
            $this->_visitCallable($method);
        }
    }

    public function visitFunction(PHP_Depend_Code_Function $function)
    {
        $this->_visitCallable($function);
    }

    private function _visitCallable(PHP_Depend_Code_AbstractCallable $callable)
    {
        $this->_metrics[$callable->getUUID()] = array('crap' => $this->_calculateCrapIndex($callable));
    }

    private function _calculateCrapIndex(PHP_Depend_Code_AbstractCallable $callable)
    {
        $ccn = $this->_ccnAnalyzer->getCCN2($callable);

        $report = $this->_createOrReturnCoverageReport();
        
        $coverage = $report->getCoverage($callable);

        if ($coverage == 0) {
            return pow($ccn, 2) + $ccn;
        } else if ($coverage > 99.5) {
            return $ccn;
        }
        return pow($ccn, 2) * pow(1 - $coverage / 100, 3) + $ccn;
    }

    private function _createOrReturnCoverageReport()
    {
        if ($this->_report === null) {
            $this->_report = $this->_createCoverageReport();
        }
        return $this->_report;
    }

    private function _createCoverageReport()
    {
        $factory = new PHP_Depend_Util_Coverage_Factory();
        return $factory->create($this->options['coverage-report']);
    }
}