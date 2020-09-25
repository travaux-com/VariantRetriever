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
        foreach (call_user_func_array('array_merge', $experiments) as $experimentName => $variants) {
                $experimentVariants = [];
                foreach (call_user_func_array('array_merge', $variants) as $variantName => $variantRollout) {
                    $experimentVariants[] = new Variant($variantName, $variantRollout);
                }
                $variantRetriever->addExperiment(new Experiment($experimentName, ...$experimentVariants));
        }
        return $variantRetriever;
    }
}
