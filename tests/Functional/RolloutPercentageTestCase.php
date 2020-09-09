<?php

use Scullwm\VariantRetriever\Exception\LogicalException;
use Scullwm\VariantRetriever\Retriever\VariantRetriever;
use Scullwm\VariantRetriever\ValueObject\Experiment;
use Scullwm\VariantRetriever\ValueObject\Variant;

test('integer following list should have a correct percentage rollout', function () {
    $variantRetriever = generateVariantRetriever();

    $results = [];
    foreach (range(1, 500) as $value) {
        // $randomIdentifier = rand(1, 3000000);
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), (string)$value);
    }

    $this->assertCount(500, $results);

    $rollout = array_count_values(readRollout($results));

    // 2% diff is allowed for 500 query
    $this->assertGreaterThanOrEqual(240, $rollout['control']); // 48
    $this->assertGreaterThanOrEqual(240, $rollout['variant']); // 48
});



test('Random numbers should have a correct percentage rollout', function () {
    $variantRetriever = generateVariantRetriever();

    $results = [];
    foreach (range(1, 1000) as $value) {
        $randomIdentifier = rand(1, 3000000);
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), (string)$randomIdentifier);
    }

    $this->assertCount(1000, $results);

    $rollout = array_count_values(readRollout($results));

    // 3% diff is allowed for 1000 query
    $this->assertGreaterThanOrEqual(465, $rollout['control']); // 47
    $this->assertGreaterThanOrEqual(465, $rollout['variant']); // 47
});


test('Random strings should have a correct percentage rollout', function () {
    $variantRetriever = generateVariantRetriever();

    $results = [];
    foreach (range(1, 200) as $value) {
        $randomIdentifier = rand(1, 3000000);
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), md5($randomIdentifier));
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), md5(uniqid()));
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), uniqid());
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), uniqid().$value);
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), sha1(uniqid()));
    }

    $this->assertCount(1000, $results);

    $rollout = array_count_values(readRollout($results));

    // 3% diff is allowed for 1000 query
    $this->assertGreaterThanOrEqual(465, $rollout['control']); // 47
    $this->assertGreaterThanOrEqual(465, $rollout['variant']); // 47
});

test('Huge volume should have a very correct percentage rollout', function () {
    $variantRetriever = generateVariantRetriever();

    $results = [];
    foreach (range(1, 100000) as $value) {
        $randomIdentifier = rand(1, 3000000);
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), md5($randomIdentifier));
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), md5(uniqid()));
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), uniqid());
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), uniqid().$value);
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), sha1(uniqid()));
    }

    $this->assertCount(500000, $results);

    $rollout = array_count_values(readRollout($results));

    // 3% diff is allowed for 1000 query
    $this->assertGreaterThanOrEqual(249350, $rollout['control']); // 49.87
    $this->assertGreaterThanOrEqual(249350, $rollout['variant']); // 49.87
});

test('Multi variant should have a correct percentage rollout', function () {
    $variantRetriever = new VariantRetriever();
    $variantRetriever->addExperiment(new Experiment('my-ab-test', ...[
        new Variant('control1', 10),
        new Variant('variant2', 10),
        new Variant('variant3', 10),
        new Variant('variant4', 10),
        new Variant('variant5', 10),
        new Variant('variant6', 10),
        new Variant('variant7', 10),
        new Variant('variant8', 10),
        new Variant('variant9', 10),
        new Variant('variant0', 10),
    ]));

    $results = [];
    foreach (range(1, 100000) as $value) {
        $randomIdentifier = rand(1, 3000000);
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), md5($randomIdentifier));
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), md5(uniqid()));
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), uniqid());
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), uniqid().$value);
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), sha1(uniqid()));
    }

    $this->assertCount(500000, $results);

    $rollout = array_count_values(readRollout($results));

    $this->assertGreaterThanOrEqual(49400, $rollout['control1']); // 9.88
    $this->assertGreaterThanOrEqual(49400, $rollout['variant2']); // 9.88
});


test('Multi variant with different rollout should have a correct percentage rollout', function () {
    $variantRetriever = new VariantRetriever();
    $variantRetriever->addExperiment(new Experiment('my-ab-test', ...[
        new Variant('control1', 10),
        new Variant('variant2', 10),
        new Variant('variant3', 80),
    ]));

    $results = [];
    foreach (range(1, 100000) as $value) {
        $randomIdentifier = rand(1, 3000000);
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), md5($randomIdentifier));
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), md5(uniqid()));
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), uniqid());
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), uniqid().$value);
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), sha1(uniqid()));
    }

    $this->assertCount(500000, $results);

    $rollout = array_count_values(readRollout($results));

    $this->assertGreaterThanOrEqual(49400, $rollout['control1']); // 9.88
    $this->assertGreaterThanOrEqual(49400, $rollout['variant2']); // 9.88
    $this->assertGreaterThanOrEqual(399600, $rollout['variant3']); // 79.92
});


test('Generate rollout fast', function () {
    $variantRetriever = generateVariantRetriever();

    $results = [];

    $start = microtime(true);
    foreach (range(1, 50000) as $value) {
        $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), md5($value));
    }
    $timeElapsedSecs = microtime(true) - $start;

    $this->assertLessThan(1, $timeElapsedSecs);
});


function readRollout(array $results)
{
    return array_map(function ($d) {
        return (string)$d;
    }, $results);
}
