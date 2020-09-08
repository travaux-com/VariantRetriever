<?php

namespace Scullwm\VariantRetriever\ValueObject;

class ABTestExperiment extends Experiment
{
    public static function new()
    {
        new parent::('ab-test');
    }
}
