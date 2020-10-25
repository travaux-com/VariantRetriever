<?php

namespace Travaux\VariantRetriever\ValueObject;

use Travaux\VariantRetriever\Exception\LogicalException;

class Experiment
{
    private string $name;

    private array $variants;

    public function __construct(string $name, Variant ...$variants)
    {
        if (!empty($variants)) {
            $variantNames = [];
            $totalPercentage = 0;
            foreach ($variants as $variant) {
                if (isset($variantNames[$variant->getName()])) {
                    throw new LogicalException(sprintf('Variant with same name "%s" already added', $variant->getName()));
                }
                $variantNames[$variant->getName()] = true;
                $totalPercentage += $variant->getRollout();
            }
            if ($totalPercentage !== 100) {
                throw new LogicalException(sprintf('Differents variants do not reach 100%% got %d', $totalPercentage));
            }
        }

        $this->name = $name;
        $this->variants = $variants;
    }

    public function getVariants(): array
    {
        return $this->variants;
    }

    public function getName(): string
    {
        return $this->toString();
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
