<p align="center">
    <h1 align="center">
        VariantRetriever
    </h1>
</p>
<br>

VariantRetriever is a minimalist package for feature flagging. It's fast, database free and idempotent library to choose over a set of variants.


## Getting Started

First of all, you need to define an Experiment with a name and it variants. Variant requires 2 arguments, a name and a rollout percentage (50% by default).
Then create a variant retriever with this experiment and variants, and ask it to retrieve a variant for a resource (in the following code, it's for a user uuid).


```php
$variantRetriever = new VariantRetriever();
$experiment = new Experiment('my-ab-test', ...[new Variant('control1'), new Variant('variant2')]);
$variantRetriever->addExperiment($experiment);

$affectedVariant = $variantRetriever->getVariantForExperiment(new Experiment('my-ab-test'), '77d8a1d5-97ba-42db-a4a7-3b9562f0ff22');

var_dump((string) $affectedVariant); // string(7) "variant2"
```

### Running the Test Suite

VariantRetriever uses [PHPUnit](https://phpunit.de) as testing framework. Once you have all dependencies installed via `composer install`, you can run the test suite with:

```bash
./vendor/bin/phpunit
```

To obtain the code coverage report, you'll need to have `xdebug` or `pcov` installed. Then, you can run:

```bash
./vendor/bin/phpunit --coverage-text
```

And this will give you detailed information about code coverage.

> Running the test suite requires PHP 8.4+ (PHPUnit 13). The library itself still supports PHP 7.4+.

## What about speed

VariantRetriever is fast. Variant allocations are built once when an experiment is registered, then each lookup is only a hash and an array access.

On a local machine, 100 000 lookups take about 40ms (about 3.7× faster than rebuilding a 50/50 allocation on every call, and about 11× faster with 10 variants). Local runs can do 500 000 lookups in well under a second. Tests also ensure that 10 000 lookups complete in less than 1 second.

## What about randomness


| Rows | Rollout | Rollout | Max Rollout difference |
| --- | --- | --- | --- |
| 1k | 50/50 | 46.5/46.5 | 3.5% |
| 500k | 50/50 | 49.87/49.87 | 0.13% |
| 500k | 10*10 | 9.88 | 0.12% |
| 500k | 10/10/80 | 9.88/9.88/79.92 | 0.12% |

