<?php

declare(strict_types=1);

namespace Database\Factory\LionDatabase\MySQL;

use Lion\Bundle\Interface\FactoryInterface;

/**
 * Factory to generate default document type data
 *
 * @package Database\Factory\LionDatabase\MySQL
 */
class DocumentTypesFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     **/
    public static function columns(): array
    {
        return [
            'document_types_name'
        ];
    }

    /**
     * {@inheritdoc}
     **/
    public static function definition(): array
    {
        return [
            [
                'Citizenship Card'
            ],
            [
                'Passport'
            ]
        ];
    }
}
