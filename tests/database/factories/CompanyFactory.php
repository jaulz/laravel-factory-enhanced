<?php

use Faker\Generator;
use Jaulz\LaravelFactory\FactoryBuilder;
use Jaulz\LaravelFactory\Tests\Stubs\Company;
use Jaulz\LaravelFactory\Tests\Stubs\User;

$factory->define(Company::class, function (Generator $faker) {
    return ['name' => $faker->company];
});
$factory->preset(Company::class, 'startup', function (FactoryBuilder $company, Generator $faker) {
    $company->with(1, 'departments')->with(1, 'departments.employees');
});
$factory->state(Company::class, 'withOwner', function (Generator $faker) {
    return [
        'owner_id' => function () {
            return factory(User::class)->create()->id;
        },
    ];
});
