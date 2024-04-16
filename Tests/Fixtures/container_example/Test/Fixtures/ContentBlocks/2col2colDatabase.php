<?php

declare(strict_types=1);

$pathinfo = pathinfo(__FILE__);
$path = $pathinfo['dirname'] . '/../Content/' . $pathinfo['basename'];
$configuration = include $path;

$configuration['tt_content'][2]['CType'] = 'codappix_image';

return $configuration;
