<?php

namespace Scullwm\VariantRetriever\Retriever;

use Scullwm\VariantRetriever\Exception\LogicalException;
use Scullwm\VariantRetriever\ValueObject\Experiment;
use Scullwm\VariantRetriever\ValueObject\Variant;

class VariantRetriever implements VariantRetrieverInterface
{
    private Experiment $experiment;

    private $allocations = [];

    public function __construct(Experiment $experiment, Variant ...$variants)
    {
        foreach ($variants as $variant) {
            $this->allocations = array_merge(array_fill(0, $variant->getRolloutPercentage(), $variant), $this->allocations);
        }

        if (count($this->allocations) != 100) {
            throw new LogicalException('Differents variants do not reach 100% got ' . count($this->allocations));
        }

        $this->experiment = $experiment;
    }

    public function getVariant(string $userIdentifier): Variant
    {
        return $this->allocations[$this->getUserIdentifierAffectation($userIdentifier)];
    }

    private function getUserIdentifierAffectation(string $userIdentifier): int
    {
        return (int)substr(crc32((string)$this->experiment.$userIdentifier), -2, 2);
    }
}
