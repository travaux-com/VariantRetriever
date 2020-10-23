<?php

namespace Travaux\VariantRetriever\ValueObject;

class Variant
{
    private string $name;

    private int $rollout;

    public function __construct(string $name, int $rollout = 50)
    {
        $this->name = $name;
        $this->rollout = $rollout;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRollout(): int
    {
        return $this->rollout;
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
