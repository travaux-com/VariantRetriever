<?php

namespace Travaux\VariantRetriever\Tests\Functional;

use Travaux\VariantRetriever\Exception\LogicalException;
use Travaux\VariantRetriever\Factory\VariantRetrieverFactory;
use Travaux\VariantRetriever\Tests\TestCase;
use Travaux\VariantRetriever\ValueObject\Experiment;

class FactoryTest extends TestCase
{
    public function testFactoryReturnACorrectVariantRetriever(): void
    {
        $variantRetrieverFactory = new VariantRetrieverFactory();

        $configuration = [
            self::DEFAULT_EXPERIMENT_NAME => [
                0 => [
                    'control' => 50,
                    'variant' => 50,
                ],
            ],
        ];

        $this->assertEquals($variantRetrieverFactory->createVariantRetriever($configuration), $this->generateVariantRetriever());
    }

    public function testFactoryWithMultipleExperimentsCanRetrieveEachOfThem(): void
    {
        $identifier = '17d8a1d5-97ba-42db-a4a7-3b9562f0ff22';
        $variantRetriever = (new VariantRetrieverFactory())->createVariantRetriever(
            [
                self::DEFAULT_EXPERIMENT_NAME => [
                    0 => [
                        'control' => 50,
                        'variant' => 50,
                    ],
                ],
            ],
            [
                'kill-switch' => [
                    0 => [
                        'on' => 100,
                    ],
                ],
            ]
        );

        $this->assertEquals('control', (string) $variantRetriever->getVariantForExperiment(new Experiment(self::DEFAULT_EXPERIMENT_NAME), $identifier));
        $this->assertEquals('on', (string) $variantRetriever->getVariantForExperiment(new Experiment('kill-switch'), $identifier));
    }

    public function testFactoryWithInvalidRolloutThrowsException(): void
    {
        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage('Differents variants do not reach 100% got 80');

        (new VariantRetrieverFactory())->createVariantRetriever([
            self::DEFAULT_EXPERIMENT_NAME => [
                0 => [
                    'control' => 50,
                    'variant' => 30,
                ],
            ],
        ]);
    }

    public function testFactoryWithEmptyConfigurationCannotRetrieveAnExperiment(): void
    {
        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage('Experiment my-ab-test do not exist');

        $variantRetriever = (new VariantRetrieverFactory())->createVariantRetriever([]);
        $variantRetriever->getVariantForExperiment(new Experiment(self::DEFAULT_EXPERIMENT_NAME), 'user-1');
    }
}
