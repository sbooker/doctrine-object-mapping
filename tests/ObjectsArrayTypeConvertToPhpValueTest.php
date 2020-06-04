<?php

declare(strict_types=1);

namespace Tests\Sbooker\DoctrineObjectMapping;

use Doctrine\DBAL\Types\ConversionException;
use Sbooker\DoctrineObjectMapping\NormalizableTypeEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ObjectsArrayTypeConvertToPhpValueTest extends TestCase
{
    /**
     * @dataProvider correctExamples
     */
    public function testConvertCorrectExamplesToPhpValue(string $value, array $deserialized, array $denormalized): void
    {
        $type = new TestObjectArrayType();
        $type->setDenormalizer($this->getArrayDenormalizer($deserialized, $denormalized, 1));
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreDenormalize, $type, 1);

        $result = $type->convertToPHPValue($value, $platform);

        $this->assertEquals($denormalized, $result);
    }

    public function correctExamples(): array
    {
        return [
            [ '[]', [], [] ],
            [ '[["normalized"]]', [ ['normalized'] ], [ new TestObject() ] ],
            [ '[["normalized"],["normalized"]]', [ ['normalized'], ['normalized'] ], [ new TestObject(), new TestObject() ] ],
            [ '[{"asd":"dsa"},{"asd":"dsa"}]', [ ["asd" => "dsa"], ["asd" => "dsa"] ], [ new TestObject(), new TestObject() ] ],
            ['{"0":{"asd":"dsa"},"1":{"asd":"dsa"}}', [ ["asd" => "dsa"], ["asd" => "dsa"] ], [ new TestObject(), new TestObject() ] ]
        ];
    }

    /**
     * @dataProvider incorrectExamoles
     */
    public function testConvertIncorrectExamplesToPhpValue(string $value, array $deserialized): void
    {
        $type = new TestObjectArrayType();
        $type->setDenormalizer($this->getArrayDenormalizer($deserialized, [], 0));
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreDenormalize, $type, 0);

        $this->expectException(ConversionException::class);
        $type->convertToPHPValue($value, $platform);
    }

    public function incorrectExamoles(): array
    {
        return [
            [ '{"asd":"dsa"}', ["asd" => "dsa"] ],
            [ '["asd":["normalized"]]', [ "asd" => ["normalized"]] ],
            [ '["asd":{"asd":"dsa"}]', [ "asd" => ["asd" => "dsa"]] ],
            [ '["4321":{"asd":"dsa"}]', [ 4321 => ["asd" => "dsa"]] ],
        ];
    }

    private function getArrayDenormalizer($expected, array $normalized, int $count = 1): DenormalizerInterface
    {
        $mock = $this->createMock(DenormalizerInterface::class);
        $mock->expects($this->exactly($count))
            ->method('supportsDenormalization')
            ->with($expected, TestObject::class)
            ->willReturn(true);

        $mock->expects($this->exactly($count))
            ->method('denormalize')
            ->with($expected, TestObject::class . '[]')
            ->willReturn($normalized);

        return $mock;
    }
}