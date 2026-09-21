<?php

namespace Travaux\VariantRetriever\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Travaux\VariantRetriever\Retriever\VariantRetriever;
use Travaux\VariantRetriever\ValueObject\Experiment;
use Travaux\VariantRetriever\ValueObject\Variant;

abstract class TestCase extends BaseTestCase
{
    protected const DEFAULT_EXPERIMENT_NAME = 'my-ab-test';

    protected function generateVariantRetriever(string $name = self::DEFAULT_EXPERIMENT_NAME): VariantRetriever
    {
        $variantRetriever = new VariantRetriever();

        return $variantRetriever->addExperiment(new Experiment($name, ...[new Variant('control'), new Variant('variant')]));
    }

    protected function readRollout(array $results): array
    {
        return array_map(function ($d) {
            return (string) $d;
        }, $results);
    }
}
