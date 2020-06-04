<?php

declare(strict_types=1);

namespace Sbooker\DoctrineObjectMapping;

use Doctrine\DBAL\Types\ConversionException;

abstract class ObjectType extends NormalizableType
{
    /**
     * @throws ConversionException
     */
    final protected function checkPhpValue($value): void
    {
        if (!is_a($value, $this->getObjectClass(), true)) {
            throw new ConversionException('Must be an object of class ' . $this->getObjectClass());
        }
    }

    /**
     * @throws ConversionException
     */
    final protected function checkDatabaseValue($value): void
    {
        foreach (array_keys($value) as $key) {
            if (is_numeric($key)) {
                throw new ConversionException('Must be associative array');
            }
        }
    }

    final protected function getDenormalizationExpression(): string
    {
        return parent::getDenormalizationExpression();
    }
}
