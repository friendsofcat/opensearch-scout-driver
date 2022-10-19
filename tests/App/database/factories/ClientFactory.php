<?php declare(strict_types=1);

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use OpenSearch\ScoutDriver\Tests\App\Client;

/** @var Factory $factory */
$factory->define(Client::class, static fn (Faker $faker) => [
    'name' => $faker->firstName,
    'last_name' => $faker->lastName,
    'phone_number' => $faker->unique()->e164PhoneNumber,
    'email' => $faker->unique()->email,
]);
