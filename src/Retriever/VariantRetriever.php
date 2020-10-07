<?php

namespace Travaux\VariantRetriever\Retriever;

use Travaux\VariantRetriever\Exception\LogicalException;
use Travaux\VariantRetriever\ValueObject\Experiment;
use Travaux\VariantRetriever\ValueObject\Variant;

class VariantRetriever implements VariantRetrieverInterface
{
    private array $experiments;

    private array $allocations = [];

    public function addExperiment(Experiment $experiment): self
    {
        $this->experiments[$experiment->getName()] = $experiment;

        return $this;
    }

    public function getVariantForExperiment(Experiment $experiment, string $userIdentifier): Variant
    {
        if (!isset($this->experiments[$experiment->getName()])) {
            throw new LogicalException(sprintf('Experiment %s do not exist', $experiment->getName()));
        }

        $variants = $this->experiments[$experiment->getName()]->getVariants();
        $this->createVariantAllocation($this->experiments[$experiment->getName()]);

        return $this->allocations[$experiment->getName()][$this->getUserIdentifierAffectation($experiment->getName(), $userIdentifier)];
    }

    private function createVariantAllocation(Experiment $experiment): void
    {
        $this->allocations[$experiment->getName()] = [];
        $variants = $experiment->getVariants();
        foreach ($variants as $variant) {
            $this->allocations[$experiment->getName()] = array_merge($this->allocations[$experiment->getName()], array_fill(0, $variant->getRolloutPercentage(), $variant));
        }
    }

    private function getUserIdentifierAffectation(string $experimentName, string $userIdentifier): int
    {
        return (int)substr(crc32((string)$experimentName.$userIdentifier), -2, 2);
    }
}
