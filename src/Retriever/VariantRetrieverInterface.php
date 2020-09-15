<?php

namespace Travaux\VariantRetriever\Retriever;

use Travaux\VariantRetriever\ValueObject\Experiment;
use Travaux\VariantRetriever\ValueObject\Variant;

interface VariantRetrieverInterface
{
    public function getVariantForExperiment(Experiment $experiment, string $userIdentifier): Variant;
}
