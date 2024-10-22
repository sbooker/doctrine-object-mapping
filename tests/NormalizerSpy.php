<?php

namespace Tests\Sbooker\DoctrineObjectMapping;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @method array getSupportedTypes(?string $format)
 */
class NormalizerSpy implements NormalizerInterface
{
    private array $objectsToNormalize = [];
    private array $objectsToSupport = [];
    private int $normalizeCallCount = 0;
    private int $supportCallCount = 0;
    public function __construct(
        private array $normalized
    ) {}

    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        $this->objectsToNormalize[] = $object;
        $this->normalizeCallCount+=1;

        return $this->normalized[$this->normalizeCallCount - 1];
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        $this->objectsToSupport[] = $data;
        $this->supportCallCount+=1;

        return true;
    }

    public function getNormalizeCallCount(): int
    {
        return $this->normalizeCallCount;
    }

    public function getSupportCallCount(): int
    {
        return $this->supportCallCount;
    }

    public function getObjectsToNormalize(): array
    {
        return $this->objectsToNormalize;
    }

    public function getObjectsToSupport(): array
    {
        return $this->objectsToSupport;
    }
}