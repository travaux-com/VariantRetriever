<?php

namespace Scullwm\VariantRetriever\Retriever;

use Scullwm\VariantRetriever\ValueObject\Variant;

interface VariantRetrieverInterface
{
    public function getVariant(string $userIdentifier): Variant;
}
