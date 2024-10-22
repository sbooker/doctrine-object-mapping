<?php

declare(strict_types=1);

namespace Tests\Sbooker\DoctrineObjectMapping;

use Doctrine\Common\EventArgs;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Sbooker\DoctrineObjectMapping\NormalizableType;
use Sbooker\DoctrineObjectMapping\NormalizableTypeEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    final protected function getConfiguredPlatform(string $eventName, NormalizableType $type, int $count = 1): AbstractPlatform
    {
        return
            $this->getPlatform(
                $this->getEventManager($eventName, new NormalizableTypeEvent($type), $count),
                $count
            );
    }

    final protected function getPlatform(EventManager $eventManager, int $count = 1): AbstractPlatform
    {
        $mock = $this->createMock(AbstractPlatform::class);
        $mock->expects($this->exactly($count))->method('getEventManager')->willReturn($eventManager);

        return $mock;
    }

    final protected function getEventManager(string $eventName, ?EventArgs $eventArgs = null, int $count = 1): EventManager
    {
        $mock = $this->createMock(EventManager::class);
        $mock->expects($this->exactly($count))->method('dispatchEvent')->with($eventName, $eventArgs);

        return $mock;
    }

    final protected function getNormalizer($expected, $normalized, int $count = 1): NormalizerInterface
    {
        $mock = $this->createMock(NormalizerInterface::class);
        $mock->method('supportsNormalization')->with($expected)->willReturn(true);
        $mock->expects($this->exactly($count))->method('normalize')->with($expected)->willReturn($normalized);

        return $mock;
    }

    final protected function getNotSupportNormalizationNormalizer($expected): NormalizerInterface
    {
        $mock = $this->createMock(NormalizerInterface::class);
        $mock->expects($this->once())->method('supportsNormalization')->with($expected)->willReturn(false);
        $mock->expects($this->never())->method('normalize');

        return $mock;
    }

    final protected function getDenormalizer($expectedValue, $denormalized, int $count = 1, string $expectedType = TestObject::class): DenormalizerInterface
    {
        $mock = $this->createMock(DenormalizerInterface::class);
        $mock->method('supportsDenormalization')->with($expectedValue)->willReturn(true);
        $mock->expects($this->exactly($count))->method('denormalize')->with($expectedValue, $expectedType)->willReturn($denormalized);

        return $mock;
    }

    final protected function getNotSupportDenormalizationDenormalizer($expected): DenormalizerInterface
    {
        $mock = $this->createMock(DenormalizerInterface::class);
        $mock->expects($this->once())->method('supportsDenormalization')->with($expected)->willReturn(false);
        $mock->expects($this->never())->method('denormalize');

        return $mock;
    }
}