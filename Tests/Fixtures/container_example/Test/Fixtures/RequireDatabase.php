<?php

declare(strict_types=1);

namespace Codappix\ContainerExample\Tests\Fixtures;

class RequireDatabase
{
    public static function getConfiguration(array $pathinfo): array
    {
        $path = $pathinfo['dirname'] . '/../Content/' . $pathinfo['basename'];
        return require $path;
    }
}
