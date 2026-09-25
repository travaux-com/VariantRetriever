<?php

namespace Travaux\VariantRetriever\Tests\Functional;

use Travaux\VariantRetriever\Exception\LogicalException;
use Travaux\VariantRetriever\Retriever\VariantRetriever;
use Travaux\VariantRetriever\Tests\TestCase;
use Travaux\VariantRetriever\ValueObject\Experiment;
use Travaux\VariantRetriever\ValueObject\Variant;

class VariantTest extends TestCase
{
    public function testVariationListThatDontMatchThe100PercentShouldThrowException(): void
    {
        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage('Differents variants do not reach 100% got 80');

        new Experiment(self::DEFAULT_EXPERIMENT_NAME, ...[new Variant('control'), new Variant('variant', 30)]);
    }

    public function testUnknownExperimentShouldThrowException(): void
    {
        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage('Experiment unknown do not exist');

        $this->generateVariantRetriever()->getVariantForExperiment(new Experiment('unknown'), 'user-1');
    }

    public function testEmptyExperimentShouldThrowExceptionWhenRegistered(): void
    {
        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage('Experiment empty-exp has no variants');

        $variantRetriever = new VariantRetriever();
        $variantRetriever->addExperiment(new Experiment('empty-exp'));
    }

    public function testNegativeRolloutShouldThrowException(): void
    {
        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage('Variant rollout must be between 0 and 100, got -10');

        new Variant('control', -10);
    }

    public function testRolloutAboveOneHundredShouldThrowException(): void
    {
        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage('Variant rollout must be between 0 and 100, got 101');

        new Variant('on', 101);
    }

    public function testZeroRolloutVariantIsAcceptedWhenExperimentReachesOneHundred(): void
    {
        $variantRetriever = new VariantRetriever();
        $variantRetriever->addExperiment(new Experiment('kill-switch', new Variant('off', 0), new Variant('on', 100)));

        $this->assertEquals('on', (string) $variantRetriever->getVariantForExperiment(new Experiment('kill-switch'), 'user-1'));
    }

    public function testAddingTheSameExperimentTwiceShouldThrowException(): void
    {
        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage('Experiment my-ab-test already exist');

        $variantRetriever = $this->generateVariantRetriever();
        $variantRetriever->addExperiment(new Experiment(self::DEFAULT_EXPERIMENT_NAME, new Variant('control'), new Variant('variant')));
    }

    public function testSingleVariantAtFullRolloutAlwaysReturnsThatVariant(): void
    {
        $variantRetriever = new VariantRetriever();
        $variantRetriever->addExperiment(new Experiment('kill-switch', new Variant('on', 100)));

        foreach (['1', '2', 'user-1', '17d8a1d5-97ba-42db-a4a7-3b9562f0ff22'] as $identifier) {
            $this->assertEquals('on', (string) $variantRetriever->getVariantForExperiment(new Experiment('kill-switch'), $identifier));
        }
    }

    public function testQueryExperimentIsMatchedByNameOnly(): void
    {
        $variantRetriever = $this->generateVariantRetriever();
        $query = new Experiment(self::DEFAULT_EXPERIMENT_NAME, new Variant('other', 40), new Variant('names', 60));

        $this->assertEquals('control', (string) $variantRetriever->getVariantForExperiment($query, '2'));
        $this->assertEquals('variant', (string) $variantRetriever->getVariantForExperiment($query, '1'));
    }

    public function testSameRetrieverInstanceCanServeMultipleExperiments(): void
    {
        $identifier = '17d8a1d5-97ba-42db-a4a7-3b9562f0ff22';
        $variantRetriever = new VariantRetriever();
        $variantRetriever->addExperiment(new Experiment(self::DEFAULT_EXPERIMENT_NAME, new Variant('control'), new Variant('variant')));
        $variantRetriever->addExperiment(new Experiment('my-other-ab-test', new Variant('control'), new Variant('variant')));

        $this->assertEquals('control', (string) $variantRetriever->getVariantForExperiment(new Experiment(self::DEFAULT_EXPERIMENT_NAME), $identifier));
        $this->assertEquals('variant', (string) $variantRetriever->getVariantForExperiment(new Experiment('my-other-ab-test'), $identifier));
    }

    public function testVariantRetrieverRetrieveAControlInThisCase(): void
    {
        $variantRetriever = $this->generateVariantRetriever();

        $this->assertEquals('control', (string) $variantRetriever->getVariantForExperiment(new Experiment(self::DEFAULT_EXPERIMENT_NAME), '2'));
    }

    public function testVariantRetrieverRetrieveAVariantInThisCase(): void
    {
        $variantRetriever = $this->generateVariantRetriever();

        $this->assertEquals('variant', (string) $variantRetriever->getVariantForExperiment(new Experiment(self::DEFAULT_EXPERIMENT_NAME), '1'));
    }

    public function testAnyVariantRetrieverInstanceAlwaysReturnTheSameVariantForAnIdentifier(): void
    {
        $identifier = '17d8a1d5-97ba-42db-a4a7-3b9562f0ff22';
        $variantRetriever = $this->generateVariantRetriever();

        $this->assertEquals('control', (string) $variantRetriever->getVariantForExperiment(new Experiment(self::DEFAULT_EXPERIMENT_NAME), $identifier));
        $this->assertEquals('control', (string) $variantRetriever->getVariantForExperiment(new Experiment(self::DEFAULT_EXPERIMENT_NAME), $identifier));

        $variantRetriever = $this->generateVariantRetriever();
        $this->assertEquals('control', (string) $variantRetriever->getVariantForExperiment(new Experiment(self::DEFAULT_EXPERIMENT_NAME), $identifier));
    }

    public function testAnIdentifierCanHaveDifferentVariantOnDifferentExperiment(): void
    {
        $identifier = '17d8a1d5-97ba-42db-a4a7-3b9562f0ff22';
        $variantRetriever = $this->generateVariantRetriever();

        $this->assertEquals('control', (string) $variantRetriever->getVariantForExperiment(new Experiment(self::DEFAULT_EXPERIMENT_NAME), $identifier));

        $variantRetriever = $this->generateVariantRetriever('my-other-ab-test');
        $this->assertEquals('variant', (string) $variantRetriever->getVariantForExperiment(new Experiment('my-other-ab-test'), $identifier));
    }

    public function testDifferentsVariantsWithSameNameShouldThrowException(): void
    {
        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage('Variant with same name "control" already added');

        new VariantRetriever(new Experiment(self::DEFAULT_EXPERIMENT_NAME, ...[
            new Variant('control'),
            new Variant('variant1', 20),
            new Variant('control', 20),
            new Variant('variant1', 10),
        ]));
    }
}
