<?php

declare(strict_types=1);

namespace Tests\Sbooker\DoctrineObjectMapping;

use Doctrine\DBAL\Types\ConversionException;
use Sbooker\DoctrineObjectMapping\NormalizableTypeEvent;

final class NormalizableTypeConvertToDatabaseValueTest extends TestCase
{
    public function testConvertNullToDatabaseValue(): void
    {
        $value = null;
        $expectedResult = null;
        $expectedNormalizeCalls = 0;

        $type = new TestNormalizableType();
        $type->setNormalizer($this->getNormalizer($value, $expectedResult, $expectedNormalizeCalls));
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreNormalize, $type, $expectedNormalizeCalls);

        $result = $type->convertToDatabaseValue($value, $platform);

        $this->assertNull($result);
    }

    /**
     * @dataProvider valueExamples
     */
    public function testConvertObjectToDatabaseValue($value): void
    {
        $normalized = ['normalized'];
        $expectedNormalizeCalls = 1;

        $type = new TestNormalizableType();
        $type->setNormalizer($this->getNormalizer($value, $normalized, $expectedNormalizeCalls));
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreNormalize, $type, $expectedNormalizeCalls);

        $result = $type->convertToDatabaseValue($value, $platform);

        $this->assertEquals(json_encode($normalized), $result);
    }

    public function valueExamples(): array
    {
        return [
            [ new TestObject() ],
            [ new \stdClass() ],
            [ [] ],
            [ 'string' ],
            [ 123 ],
            [ 123.123 ],
        ];
    }

    public function testNormalizationNotSupport(): void
    {
        $value = new TestObject();
        $expectedNormalizeCalls = 1;

        $type = new TestNormalizableType();
        $type->setNormalizer($this->getNotSupportNormalizationNormalizer($value));
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreNormalize, $type, $expectedNormalizeCalls);

        $this->expectException(ConversionException::class);
        $type->convertToDatabaseValue($value, $platform);
    }
}