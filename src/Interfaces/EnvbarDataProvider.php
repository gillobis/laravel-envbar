<?php

namespace Gillobis\Envbar\Interfaces;

interface EnvbarDataProvider
{
    public function label(): string;

    /**
     * @return array<string>|string
     */
    public function value(): string|array;

    public function icon(): ?string;
}
