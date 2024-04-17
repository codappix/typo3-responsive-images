<?php

declare(strict_types=1);

use Codappix\ContainerExample\Tests\Fixtures\RequireDatabase;

$configuration = RequireDatabase::getConfiguration(pathinfo(__FILE__));

$configuration['tt_content'][1]['CType'] = 'codappix_imagefixedwidth';

return $configuration;
