<?php

declare(strict_types=1);

namespace Tests\Sbooker\DoctrineObjectMapping;

use Sbooker\DoctrineObjectMapping\ObjectType;

final class TestObjectType extends ObjectType {

    protected function getObjectClass(): string
    {
        return TestObject::class;
    }
}