<?php

declare(strict_types=1);

namespace Tests\Sbooker\DoctrineObjectMapping;

use Sbooker\DoctrineObjectMapping\ObjectsArrayType;

final class TestObjectArrayType extends ObjectsArrayType
{
    protected function getObjectClass(): string
    {
        return TestObject::class;
    }
}