<?php

namespace Scullwm\VariantRetriever\Retriever;

use Scullwm\VariantRetriever\ValueObject\Experiment;
use Scullwm\VariantRetriever\ValueObject\Variant;

interface VariantRetrieverInterface
{
    public function getVariantForExperiment(Experiment $experiment, string $userIdentifier): Variant;
}
