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
        $spy = new NormalizerSpy($normalized);
        $type->setNormalizer($spy);
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreNormalize, $type, 1);

        $result = $type->convertToDatabaseValue($value, $platform);

        $this->assertEquals($expectedNormalizeCalls, $spy->getNormalizeCallCount());
        $this->assertEquals($expectedNormalizeCalls, $spy->getSupportCallCount());
        $this->assertEquals($value, $spy->getObjectsToNormalize());
        $this->assertEquals($value, $spy->getObjectsToSupport());

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

    private function getArrayNormalizer(array $normalized): NormalizerSpy
    {
        return new NormalizerSpy($normalized);
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