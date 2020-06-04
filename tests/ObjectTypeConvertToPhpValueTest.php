<?php

declare(strict_types=1);

namespace Tests\Sbooker\DoctrineObjectMapping;

use Doctrine\DBAL\Types\ConversionException;
use Sbooker\DoctrineObjectMapping\NormalizableTypeEvent;

final class ObjectTypeConvertToPhpValueTest extends TestCase
{
    /**
     * @dataProvider correctValueExamples
     */
    public function testConvertToPhpValue(string $value, array $deserialized): void
    {
        $denormalized = new TestObject();
        $expectedNormalizeCalls = 1;

        $type = new TestObjectType();
        $type->setDenormalizer($this->getDenormalizer($deserialized, $denormalized, $expectedNormalizeCalls));
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreDenormalize, $type, $expectedNormalizeCalls);

        $result = $type->convertToPHPValue($value, $platform);

        $this->assertEquals($denormalized, $result);
    }

    public function correctValueExamples(): array
    {
        return [
            [ '{}', [] ],
            [ '{"asd":"dsa"}', ["asd" => "dsa"] ],
            [ '{"asd":"dsa","dsa":"asd"}', ["asd" => "dsa","dsa" => "asd"] ],
        ];
    }

    /**
     * @dataProvider incorrectValueExamples
     */
    public function testConvertIncorrectToPhpValue(string $value, array $deserialized): void
    {
        $denormalized = new TestObject();
        $expectedNormalizeCalls = 0;

        $type = new TestObjectType();
        $type->setDenormalizer($this->getDenormalizer($deserialized, $denormalized, $expectedNormalizeCalls));
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreDenormalize, $type, $expectedNormalizeCalls);

        $this->expectException(ConversionException::class);
        $type->convertToPHPValue($value, $platform);
    }

    public function incorrectValueExamples(): array
    {
        return [
            [ '["asd"]', ["asd"] ],
            [ '["asd","dsa"]', ["asd","dsa"] ],
            [ '{"123":"asd"}', ["123" => "asd"] ],
            [ '{"123.321":"asd"}', ["123.321" => "asd"] ],
        ];
    }
}