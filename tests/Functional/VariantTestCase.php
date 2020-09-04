<?php

use Scullwm\VariantRetriever\Exception\LogicalException;
use Scullwm\VariantRetriever\Retriever\VariantRetriever;
use Scullwm\VariantRetriever\ValueObject\Experiment;
use Scullwm\VariantRetriever\ValueObject\Variant;

test('variation list that dont match the 100% should throw exception', function () {
    new VariantRetriever(new Experiment('my-ab-test'), ...[new Variant('control'), new Variant('variant', 30)]);
})->throws(LogicalException::class);

test('variant retriever retrievea control in this case', function () {
    $variantRetriever = generateVariantRetriever();

    $this->assertEquals('control', (string)$variantRetriever->getVariant('1'));
});

test('variant retriever retrieve a variant in this case', function () {
    $variantRetriever = generateVariantRetriever();

    $this->assertEquals('variant', (string)$variantRetriever->getVariant('4567'));
});


test('any variant retriever instance always return the same variant for an identifier', function () {
    $identifier = '77d8a1d5-97ba-42db-a4a7-3b9562f0ff22';
    $variantRetriever = generateVariantRetriever();

    $this->assertEquals('control', (string)$variantRetriever->getVariant($identifier));
    $this->assertEquals('control', (string)$variantRetriever->getVariant($identifier));

    $variantRetriever = generateVariantRetriever();
    $this->assertEquals('control', (string)$variantRetriever->getVariant($identifier));
});


test('an identifier can have different variant on different experiment', function () {
    $identifier = '77d8a1d5-97ba-42db-a4a7-3b9562f0ff22';
    $variantRetriever = generateVariantRetriever();

    $this->assertEquals('control', (string)$variantRetriever->getVariant($identifier));

    $variantRetriever = generateVariantRetriever('my-other-ab-test');
    $this->assertEquals('variant', (string)$variantRetriever->getVariant($identifier));
});


function generateVariantRetriever($name = 'my-ab-test')
{
    return new VariantRetriever(new Experiment($name), ...[new Variant('control'), new Variant('variant')]);
}
