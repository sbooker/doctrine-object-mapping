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
    final protected function checkPhpValue($value): void
    {
        if (!is_array($value)) {
            throw new ConversionException('Must be an array');
        }
        foreach ($value as $item) {
            if (!is_a($item, $this->getObjectClass(), true)) {
                throw new ConversionException('Item be an object of class ' . $this->getObjectClass());
            }
        }
    }

    /**
     * @throws ConversionException
     */
    final protected function checkDatabaseValue($value): void
    {
        if (!is_array($value)) {
            throw new ConversionException('Must be an array');
        }

        if (empty($value)) {
            return;
        }

        if (array_keys($value) !== range(0, count($value) - 1)) {
            throw new ConversionException('Must be an index array');
        }
    }

    final protected function getDenormalizationExpression(): string
    {
        return parent::getDenormalizationExpression() . '[]';
    }
}
