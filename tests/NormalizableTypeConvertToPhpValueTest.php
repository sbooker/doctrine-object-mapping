<?php

declare(strict_types=1);

namespace Tests\Sbooker\DoctrineObjectMapping;

use Doctrine\DBAL\Types\ConversionException;
use Sbooker\DoctrineObjectMapping\NormalizableTypeEvent;

final class NormalizableTypeConvertToPhpValueTest extends TestCase
{
    public function testConvertNullToPhpValue(): void
    {
        $value = null;
        $expectedResult = null;
        $expectedDenormalizeCalls = 0;

        $type = new TestNormalizableType();
        $type->setDenormalizer($this->getDenormalizer($value, $expectedResult, $expectedDenormalizeCalls));
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreDenormalize, $type, $expectedDenormalizeCalls);

        $result = $type->convertToPHPValue($value, $platform);

        $this->assertNull($result);
    }

    /**
     * @dataProvider valueExamples
     */
    public function testConvertToPhpValue(string $value, array $deserialized): void
    {
        $expectedCalls = 1;
        $denormalized = new TestObject();

        $type = new TestNormalizableType();
        $type->setDenormalizer($this->getDenormalizer($deserialized, $denormalized, $expectedCalls));
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreDenormalize, $type, $expectedCalls);

        $result = $type->convertToPHPValue($value, $platform);

        $this->assertEquals($denormalized, $result);
    }

    public function valueExamples(): array
    {
        return [
            [ '[]', [] ],
            [ '{}', [] ],
            [ '["string"]', ["string"] ],
            [ '[123, 312]', [123, 312] ],
            [ '{"asd":"dsa", "dsa":"asd"}', ["asd" => "dsa", "dsa" => "asd"] ],
        ];
    }

    public function testDenormalizationNotSupport(): void
    {
        $value = '{}';
        $deserialized = [];
        $expectedNormalizeCalls = 1;

        $type = new TestNormalizableType();
        $type->setDenormalizer($this->getNotSupportDenormalizationDenormalizer($deserialized));
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreDenormalize, $type, $expectedNormalizeCalls);

        $this->expectException(ConversionException::class);
        $type->convertToPHPValue($value, $platform);
    }
}