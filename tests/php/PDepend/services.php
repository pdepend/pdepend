<?php

use PDepend\Report\Dummy\Logger;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('pdepend.test.dummy_logger', Logger::class)
        ->public()
        ->tag('pdepend.logger', [
            'option' => '--dummy-logger',
            'message' => 'Dummy logger for tests',
        ]);
};
