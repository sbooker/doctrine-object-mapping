<?php

declare(strict_types=1);

namespace Tests\Sbooker\DoctrineObjectMapping;

use Sbooker\DoctrineObjectMapping\NormalizableType;

final class TestNormalizableType extends NormalizableType
{
    protected function getObjectClass(): string
    {
        return TestObject::class;
    }

    protected function checkDatabaseValue($value): void
    {
        // Do nothing
    }

    protected function checkPhpValue($value): void
    {
        // Do nothing
    }
}