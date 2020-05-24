<?php

declare(strict_types=1);

namespace Sbooker\DoctrineObjectMapping;

use Doctrine\DBAL\Types\ConversionException;

abstract class ObjectsArrayType extends NormalizableType
{
    final protected function normalize($value): array
    {
        return
            array_map(
                function (object $item): array {
                    return parent::normalize($item);
                },
                $value
            );
    }

    /**
     * @throws ConversionException
     */
    final protected function checkDatabaseValue($value): void
    {
        if (!empty($value) && (!isset($value[0]) || !is_array($value[0]))) {
            throw new ConversionException('Must be index array');
        }
    }

    /**
     * @throws ConversionException
     */
    final protected function checkPhpValue($value): void
    {
        if (!is_array($value)) {
            throw new ConversionException('Must be an array');
        }
    }

    final protected function getDenormalizationExpression(): string
    {
        return parent::getDenormalizationExpression() . '[]';
    }
}
