<?php

declare(strict_types=1);

namespace Sbooker\DoctrineObjectMapping;

use Doctrine\DBAL\Types\ConversionException;

abstract class ObjectType extends NormalizableType
{
    final protected function normalize($value): array
    {
        return parent::normalize($value);
    }

    /**
     * @throws ConversionException
     */
    final protected function checkDatabaseValue($value): void
    {
        if (empty($value) || array_keys($value) === range(0, count($value) - 1)) {
            throw new ConversionException('Must be associative array');
        }
    }

    /**
     * @throws ConversionException
     */
    final protected function checkPhpValue($value): void
    {
        if (!is_object($value)) {
            throw new ConversionException('Must be an object');
        }
    }

    final protected function getDenormalizationExpression(): string
    {
        return parent::getDenormalizationExpression();
    }
}
