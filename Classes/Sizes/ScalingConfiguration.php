<?php

declare(strict_types=1);

namespace Codappix\ResponsiveImages\Sizes;

class ScalingConfiguration
{
    /**
     * @var float[]
     */
    private array $multiplier = [];

    /**
     * @var int[]
     */
    private array $sizes = [];

    public function __construct(array $configuration)
    {
        if (isset($configuration['multiplier'])) {
            $this->multiplier = array_map(static fn ($multiplier): float => (float) $multiplier, $configuration['multiplier']);
        }

        if (isset($configuration['sizes'])) {
            $this->sizes = array_map(static fn ($size): int => (int) $size, $configuration['sizes']);
        }
    }

    /**
     * @return float[]
     */
    public function getMultiplier(): array
    {
        return $this->multiplier;
    }

    /**
     * @return int[]
     */
    public function getSizes(): array
    {
        return $this->sizes;
    }
}
