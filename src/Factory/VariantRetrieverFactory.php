<?php

namespace Travaux\VariantRetriever\Factory;

use Travaux\VariantRetriever\Retriever\VariantRetriever;
use Travaux\VariantRetriever\Retriever\VariantRetrieverInterface;
use Travaux\VariantRetriever\ValueObject\Experiment;
use Travaux\VariantRetriever\ValueObject\Variant;

final class VariantRetrieverFactory
{
    public function createVariantRetriever(array ...$experiments): VariantRetrieverInterface
    {
        $variantRetriever = new VariantRetriever();
        foreach ($this->mergeConfigurations($experiments) as $experimentName => $variants) {
            $experimentVariants = [];
            foreach ($this->mergeConfigurations(array_values($variants)) as $variantName => $variantRollout) {
                $experimentVariants[] = new Variant($variantName, $variantRollout);
            }
            $variantRetriever->addExperiment(new Experiment($experimentName, ...$experimentVariants));
        }
        return $variantRetriever;
    }

    private function mergeConfigurations(array $configurations): array
    {
        if ($configurations === []) {
            return [];
        }

        return array_merge(...$configurations);
    }
}
