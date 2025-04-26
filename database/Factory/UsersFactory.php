<?php

declare(strict_types=1);

namespace Database\Factory;

use Lion\Bundle\Interface\FactoryInterface;

/**
 * Description of the factory 'UsersFactory'
 *
 * @package Database\Factory
 */
class UsersFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public static function columns(): array
    {
        return [
            'users_name',
            'created_at',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function definition(): array
    {
        return [
            [
                fake()->userName(),
                fake()->date(),
            ],
            [
                fake()->userName(),
                fake()->date(),
            ],
            [
                fake()->userName(),
                fake()->date(),
            ],
            [
                fake()->userName(),
                fake()->date(),
            ],
            [
                fake()->userName(),
                fake()->date(),
            ],
            [
                fake()->userName(),
                fake()->date(),
            ],
            [
                fake()->userName(),
                fake()->date(),
            ],
            [
                fake()->userName(),
                fake()->date(),
            ],
            [
                fake()->userName(),
                fake()->date(),
            ],
            [
                fake()->userName(),
                fake()->date(),
            ],
        ];
    }
}
