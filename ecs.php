<?php
declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withRootFiles()
    ->withSets([
        __DIR__ . '/tools/coding-standards/vendor/lmc/coding-standard/ecs.php',
    ])
    ->withSkip([
        'PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff.Found' => [
            'src/Services/AppStatusCollector.php',
        ],
    ]);
