<?php

namespace PDepend;

class ApplicationTest extends AbstractTest
{
    public function testGetRunner()
    {
        $application = $this->createTestApplication();
        $runner = $application->getRunner();

        $this->assertInstanceOf('PDepend\TextUI\Runner', $runner);
    }

    public function testAnalyzerFactory()
    {
        $application = $this->createTestApplication();

        $this->assertInstanceOf('PDepend\Metrics\AnalyzerFactory', $application->getAnalyzerFactory());
    }

    public function testReportGeneratorFactory()
    {
        $application = $this->createTestApplication();

        $this->assertInstanceOf('PDepend\Report\ReportGeneratorFactory', $application->getReportGeneratorFactory());
    }
}
