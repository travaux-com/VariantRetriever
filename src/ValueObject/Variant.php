<?php

namespace Travaux\VariantRetriever\ValueObject;

class Variant
{
    private string $name;
    private int $rolloutPercentage;

    public function __construct(string $name, int $rolloutPercentage = 50)
    {
        $this->name = $name;
        $this->rolloutPercentage = $rolloutPercentage;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRolloutPercentage(): int
    {
        return $this->rolloutPercentage;
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
