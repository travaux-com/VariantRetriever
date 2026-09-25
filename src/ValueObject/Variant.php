<?php

namespace Travaux\VariantRetriever\ValueObject;

use Travaux\VariantRetriever\Exception\LogicalException;

class Variant
{
    private string $name;

    private int $rollout;

    public function __construct(string $name, int $rollout = 50)
    {
        if ($rollout < 0 || $rollout > 100) {
            throw new LogicalException(sprintf('Variant rollout must be between 0 and 100, got %d', $rollout));
        }

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
