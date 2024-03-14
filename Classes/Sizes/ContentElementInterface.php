<?php

declare(strict_types=1);

namespace Codappix\ResponsiveImages\Sizes;

/**
 * This class represents the content elements in the rootline of the current
 * content element which is rendered.
 */
interface ContentElementInterface
{
    public function __construct(array $data);

    public static function make(mixed ...$arguments): static;

    public function getData(?string $dataIdentifier = null): mixed;

    public function getContentType(): string;

    public function getColPos(): int;

    public function setParent(self $contentElement): void;

    public function getParent(): ?self;
}
