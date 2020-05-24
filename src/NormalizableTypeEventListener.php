<?php

declare(strict_types=1);

namespace Sbooker\DoctrineObjectMapping;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class NormalizableTypeEventListener
{
    /** @var NormalizerInterface */
    private $normalizer;

    /** @var DenormalizerInterface */
    private $denormalizer;

    public function __construct(NormalizerInterface $normalizer, DenormalizerInterface $denormalizer)
    {
        $this->normalizer = $normalizer;
        $this->denormalizer = $denormalizer;
    }

    public function onPreNormalize(NormalizableTypeEvent $event): void
    {
        $event->getType()->setNormalizer($this->normalizer);
    }

    public function onPreDenormalize(NormalizableTypeEvent $event): void
    {
        $event->getType()->setDenormalizer($this->denormalizer);
    }
}