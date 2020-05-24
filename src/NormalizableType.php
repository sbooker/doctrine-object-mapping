<?php

declare(strict_types=1);

namespace Sbooker\DoctrineObjectMapping;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class NormalizableType extends JsonType implements NormalizerAwareInterface, DenormalizerAwareInterface
{
    use NormalizerAwareTrait, DenormalizerAwareTrait;

    abstract protected function getObjectClass(): string;

    /**
     * @throws ConversionException
     */
    abstract protected function checkDatabaseValue($value): void;

    /**
     * @throws ConversionException
     */
    abstract protected function checkPhpValue($value): void;

    /**
     * @throws ConversionException
     */
    final public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = parent::convertToPHPValue($value, $platform);

        if (null === $value) {
            return null;
        }
        if (!is_array($value)) {
            throw new ConversionException('Must be an array');
        }

        $this->checkDatabaseValue($value);

        $this->dispatchEvent($platform->getEventManager(), NormalizableTypeEvent::onPreDenormalize);

        return $this->denormalize($value);
    }

    /**
     * @throws ConversionException
     */
    final public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        $this->checkPhpValue($value);

        $this->dispatchEvent($platform->getEventManager(), NormalizableTypeEvent::onPreNormalize);

        return parent::convertToDatabaseValue($this->normalize($value), $platform);
    }

    private function dispatchEvent(EventManager $eventManager, string $eventName): void
    {
        $eventManager->dispatchEvent($eventName, new NormalizableTypeEvent($this));
    }

    protected function normalize($value) /* : mixed */
    {
        if (!$this->getNormalizer()->supportsNormalization($value)) {
            throw new ConversionException('Normalization not supported');
        }

        return $this->getNormalizer()->normalize($value);
    }

    /**
     * @throws ConversionException
     */
    private function denormalize(array $value) /* : mixed */
    {
        if (!$this->getDenormalizer()->supportsDenormalization($value, $this->getObjectClass())) {
            throw new ConversionException('Denormalization not supported');
        }

        return $this->getDenormalizer()->denormalize($value, $this->getDenormalizationExpression(), JsonEncoder::FORMAT);
    }

    protected function getDenormalizationExpression(): string
    {
        return $this->getObjectClass();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return parent::getSQLDeclaration(array_merge($fieldDeclaration, ['jsonb' => true]), $platform);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    private function getDenormalizer(): DenormalizerInterface
    {
        if (null === $this->denormalizer) {
            throw new \RuntimeException('Denormalizer not sets');
        }

        return $this->denormalizer;
    }

    private function getNormalizer(): NormalizerInterface
    {
        if (null === $this->normalizer) {
            throw new \RuntimeException('Normalizer not sets');
        }

        return $this->normalizer;
    }
}
