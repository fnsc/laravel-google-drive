<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\PSR4\Rector\FileWithoutNamespace\NormalizeNamespaceByPSR4ComposerAutoloadRector;
use Rector\Set\ValueObject\LevelSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();
    $parameters->set(
        Option::PATHS,
        [
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ]
    );

    // Define what rule sets will be applied
    $containerConfigurator->import(LevelSetList::UP_TO_PHP_81);

    // get services (needed for register a single rule)
     $services = $containerConfigurator->services();

    // register a single rule
     $services->set(TypedPropertyRector::class);
     $services->set(NormalizeNamespaceByPSR4ComposerAutoloadRector::class);
};
