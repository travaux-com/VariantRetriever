<?php

use Travaux\VariantRetriever\Factory\VariantRetrieverFactory;

test('factory return a correct variant retriever', function () {
    $variantRetrieverFactory = new VariantRetrieverFactory();

    $configuration = [
        DEFAULT_EXPERIMENT_NAME => [
            0 =>
            [
                'control' => 50,
                'variant' => 50
            ]
        ]
    ];

    $this->assertEquals($variantRetrieverFactory->createVariantRetriever($configuration), generateVariantRetriever());
});
