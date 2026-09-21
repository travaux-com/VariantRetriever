<?php

namespace Travaux\VariantRetriever\Tests\Functional;

use Travaux\VariantRetriever\Retriever\VariantRetriever;
use Travaux\VariantRetriever\Tests\TestCase;
use Travaux\VariantRetriever\ValueObject\Experiment;
use Travaux\VariantRetriever\ValueObject\Variant;

class RolloutPercentageTest extends TestCase
{
    public function testIntegerFollowingListShouldHaveACorrectPercentageRollout(): void
    {
        $variantRetriever = $this->generateVariantRetriever();

        $results = [];
        foreach (range(1, 500) as $value) {
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), (string) $value);
        }

        $this->assertCount(500, $results);

        $rollout = array_count_values($this->readRollout($results));

        $this->assertGreaterThanOrEqual(240, $rollout['control']);
        $this->assertGreaterThanOrEqual(240, $rollout['variant']);
    }

    public function testSmallListShouldHaveACorrectPercentageRollout(): void
    {
        $variantRetriever = $this->generateVariantRetriever();

        $results = [];
        foreach (range(1, 100) as $value) {
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), (string) $value);
        }

        $this->assertCount(100, $results);

        $rollout = array_count_values($this->readRollout($results));

        $this->assertGreaterThanOrEqual(40, $rollout['control']);
        $this->assertGreaterThanOrEqual(40, $rollout['variant']);
    }

    public function testRandomNumbersShouldHaveACorrectPercentageRollout(): void
    {
        $variantRetriever = $this->generateVariantRetriever();

        $results = [];
        foreach (range(1, 1000) as $value) {
            $randomIdentifier = rand(1, 3000000);
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), (string) $randomIdentifier);
        }

        $this->assertCount(1000, $results);

        $rollout = array_count_values($this->readRollout($results));

        $this->assertGreaterThanOrEqual(450, $rollout['control']);
        $this->assertGreaterThanOrEqual(450, $rollout['variant']);
    }

    public function testRandomStringsShouldHaveACorrectPercentageRollout(): void
    {
        $variantRetriever = $this->generateVariantRetriever();

        $results = [];
        foreach (range(1, 200) as $value) {
            $randomIdentifier = rand(1, 3000000);
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), md5($randomIdentifier));
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), md5(uniqid()));
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), uniqid());
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), uniqid() . $value);
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), sha1(uniqid()));
        }

        $this->assertCount(1000, $results);

        $rollout = array_count_values($this->readRollout($results));

        $this->assertGreaterThanOrEqual(450, $rollout['control']);
        $this->assertGreaterThanOrEqual(450, $rollout['variant']);
    }

    public function testHugeVolumeShouldHaveAVeryCorrectPercentageRollout(): void
    {
        $variantRetriever = $this->generateVariantRetriever();

        $results = [];
        foreach (range(1, 100000) as $value) {
            $randomIdentifier = rand(1, 3000000);
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), md5($randomIdentifier));
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), md5(uniqid()));
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), uniqid());
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), uniqid() . $value);
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), sha1(uniqid()));
        }

        $this->assertCount(500000, $results);

        $rollout = array_count_values($this->readRollout($results));

        $this->assertGreaterThanOrEqual(245000, $rollout['control']);
        $this->assertGreaterThanOrEqual(245000, $rollout['variant']);
    }

    public function testMultiVariantShouldHaveACorrectPercentageRollout(): void
    {
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
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), uniqid() . $value);
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), sha1(uniqid()));
        }

        $this->assertCount(500000, $results);

        $rollout = array_count_values($this->readRollout($results));

        $this->assertGreaterThanOrEqual(49000, $rollout['control1']);
        $this->assertGreaterThanOrEqual(49000, $rollout['variant2']);
    }

    public function testMultiVariantWithDifferentRolloutShouldHaveACorrectPercentageRollout(): void
    {
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
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), uniqid() . $value);
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), sha1(uniqid()));
        }

        $this->assertCount(500000, $results);

        $rollout = array_count_values($this->readRollout($results));

        $this->assertGreaterThanOrEqual(49400, $rollout['control1']);
        $this->assertGreaterThanOrEqual(49400, $rollout['variant2']);
        $this->assertGreaterThanOrEqual(399000, $rollout['variant3']);
    }

    public function testGenerateRolloutFast(): void
    {
        $variantRetriever = $this->generateVariantRetriever();

        $results = [];

        $start = microtime(true);
        foreach (range(1, 10000) as $value) {
            $results[] = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), md5($value));
        }
        $timeElapsedSecs = microtime(true) - $start;

        $this->assertLessThan(1, $timeElapsedSecs);
    }
}
