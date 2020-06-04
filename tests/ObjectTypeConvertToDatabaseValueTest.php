<?php

declare(strict_types=1);

namespace Tests\Sbooker\DoctrineObjectMapping;

use Doctrine\DBAL\Types\ConversionException;
use Sbooker\DoctrineObjectMapping\NormalizableTypeEvent;

final class ObjectTypeConvertToDatabaseValueTest extends TestCase
{
    /**
     * @dataProvider correctValueExamples
     */
    public function testConvertObjectToDatabaseValue(object $value): void
    {
        $normalized = ['normalized'];
        $expectedNormalizeCalls = 1;

        $type = new TestObjectType();
        $type->setNormalizer($this->getNormalizer($value, $normalized, $expectedNormalizeCalls));
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreNormalize, $type, $expectedNormalizeCalls);

        $result = $type->convertToDatabaseValue($value, $platform);

        $this->assertEquals(json_encode($normalized), $result);
    }

    public function correctValueExamples(): array
    {
        return [
            [ new TestObject() ],
        ];
    }

    /**
     * @dataProvider incorrectValueExamples
     */
    public function testConvertNonObjectToDatabaseValue($value): void
    {
        $normalized = ['normalized'];
        $expectedNormalizeCalls = 0;

        $type = new TestObjectType();
        $type->setNormalizer($this->getNormalizer($value, $normalized, $expectedNormalizeCalls));
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreNormalize, $type, $expectedNormalizeCalls);

        $this->expectException(ConversionException::class);
        $type->convertToDatabaseValue($value, $platform);
    }

    public function incorrectValueExamples(): array
    {
        return [
            [ new \stdClass() ],
            [ [] ],
            [ [ '123', '312'] ],
            [ [ 'a' => 'b', 'b' => 'a'] ],
            [ 'string' ],
            [ 123 ],
            [ 123.123 ],
        ];
    }
}

