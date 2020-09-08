<?php

use Scullwm\VariantRetriever\Exception\LogicalException;
use Scullwm\VariantRetriever\Retriever\VariantRetriever;
use Scullwm\VariantRetriever\ValueObject\Experiment;
use Scullwm\VariantRetriever\ValueObject\Variant;

const DEFAULT_EXPERIMENT_NAME = 'my-ab-test';

test('variation list that dont match the 100% should throw exception', function () {
    new VariantRetriever(new Experiment(DEFAULT_EXPERIMENT_NAME, ...[new Variant('control'), new Variant('variant', 30)]));
})->throws(LogicalException::class);

test('variant retriever retrieve a control in this case', function () {
    $variantRetriever = generateVariantRetriever();

    $this->assertEquals('control', (string)$variantRetriever->getVariantForExperiment(new Experiment(DEFAULT_EXPERIMENT_NAME), '2'));
});

test('variant retriever retrieve a variant in this case', function () {
    $variantRetriever = generateVariantRetriever();

    $this->assertEquals('variant', (string)$variantRetriever->getVariantForExperiment(new Experiment(DEFAULT_EXPERIMENT_NAME), '1'));
});


test('any variant retriever instance always return the same variant for an identifier', function () {
    $identifier = '17d8a1d5-97ba-42db-a4a7-3b9562f0ff22';
    $variantRetriever = generateVariantRetriever();

    $this->assertEquals('control', (string)$variantRetriever->getVariantForExperiment(new Experiment(DEFAULT_EXPERIMENT_NAME), $identifier));
    $this->assertEquals('control', (string)$variantRetriever->getVariantForExperiment(new Experiment(DEFAULT_EXPERIMENT_NAME), $identifier));

    $variantRetriever = generateVariantRetriever();
    $this->assertEquals('control', (string)$variantRetriever->getVariantForExperiment(new Experiment(DEFAULT_EXPERIMENT_NAME), $identifier));
});


test('an identifier can have different variant on different experiment', function () {
    $identifier = '17d8a1d5-97ba-42db-a4a7-3b9562f0ff22';
    $variantRetriever = generateVariantRetriever();

    $this->assertEquals('control', (string)$variantRetriever->getVariantForExperiment(new Experiment(DEFAULT_EXPERIMENT_NAME), $identifier));

    $variantRetriever = generateVariantRetriever('my-other-ab-test');
    $this->assertEquals('variant', (string)$variantRetriever->getVariantForExperiment(new Experiment('my-other-ab-test'), $identifier));
});


function generateVariantRetriever($name = DEFAULT_EXPERIMENT_NAME)
{
    $variantRetriever = new VariantRetriever();
    return $variantRetriever->addExperiment(new Experiment($name, ...[new Variant('control'), new Variant('variant')]));
}
