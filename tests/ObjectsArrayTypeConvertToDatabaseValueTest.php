<?php

declare(strict_types=1);

namespace Tests\Sbooker\DoctrineObjectMapping;

use Doctrine\DBAL\Types\ConversionException;
use Sbooker\DoctrineObjectMapping\NormalizableTypeEvent;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ObjectsArrayTypeConvertToDatabaseValueTest extends TestCase
{
    /**
     * @dataProvider correctValueExamples
     */
    public function testConvertObjectToDatabaseValue(array $value, array $normalized): void
    {
        $expectedNormalizeCalls = count($value);

        $type = new TestObjectArrayType();
        $type->setNormalizer($this->getArrayNormalizer($value, $normalized, $expectedNormalizeCalls));
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreNormalize, $type, 1);

        $result = $type->convertToDatabaseValue($value, $platform);

        $this->assertEquals(json_encode($normalized), $result);
    }

    public function correctValueExamples(): array
    {
        return [
            [ [], [] ],
            [ [ new TestObject() ], [ ['normalized'] ] ],
            [ [ new TestObject(), new TestObject() ], [ ['normalized'], ['normalized'] ] ],
        ];
    }

    private function getArrayNormalizer($expected, array $normalized, int $count = 1): NormalizerInterface
    {
        $expectedChain = array_map(function ($item): array { return [ $item ]; }, $expected);

        $mock = $this->createMock(NormalizerInterface::class);
        $mock->expects($this->exactly($count))
            ->method('supportsNormalization')
            ->withConsecutive(...$expectedChain)
            ->willReturn(true);

        $mock->expects($this->exactly($count))
            ->method('normalize')
            ->withConsecutive(...$expectedChain)
            ->willReturnOnConsecutiveCalls(...$normalized);

        return $mock;
    }

    /**
     * @dataProvider incorrectValueExamples
     */
    public function testConvertNonObjectToDatabaseValue($value): void
    {
        $normalized = ['normalized'];
        $expectedNormalizeCalls = 0;

        $type = new TestObjectArrayType();
        $type->setNormalizer($this->getNormalizer($value, $normalized, $expectedNormalizeCalls));
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreNormalize, $type, 0);

        $this->expectException(ConversionException::class);
        $type->convertToDatabaseValue($value, $platform);
    }

    public function incorrectValueExamples(): array
    {
        return [
            [ new TestObject() ],
            [ new \stdClass() ],
            [ [ new \stdClass() ] ],
            [ 'string' ],
            [ 123 ],
            [ 123.123 ],
            [ [ 'string', 'other string' ] ],
            [ [ new TestObject(), new \stdClass() ] ],
        ];
    }
}