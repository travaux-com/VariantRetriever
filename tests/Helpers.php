<?php

use Travaux\VariantRetriever\Retriever\VariantRetriever;
use Travaux\VariantRetriever\ValueObject\Experiment;
use Travaux\VariantRetriever\ValueObject\Variant;

const DEFAULT_EXPERIMENT_NAME = 'my-ab-test';

function generateVariantRetriever($name = DEFAULT_EXPERIMENT_NAME)
{
    $variantRetriever = new VariantRetriever();
    return $variantRetriever->addExperiment(new Experiment($name, ...[new Variant('control'), new Variant('variant')]));
}

function readRollout(array $results)
{
    return array_map(function ($d) {
        return (string)$d;
    }, $results);
}
