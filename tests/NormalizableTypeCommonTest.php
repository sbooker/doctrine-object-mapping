<?php

declare(strict_types=1);

namespace Tests\Sbooker\DoctrineObjectMapping;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Sbooker\DoctrineObjectMapping\NormalizableTypeEvent;

final class NormalizableTypeCommonTest extends TestCase
{
    /**
     * @dataProvider eventNamesExamples
     */
    public function testRequiresSQLCommentHint(string $eventName): void
    {
        $type = new TestNormalizableType();
        $platform = $this->getConfiguredPlatform($eventName, $type, 0);

        $this->assertTrue($type->requiresSQLCommentHint($platform));
    }

    public function eventNamesExamples(): array
    {
        return [
            [ NormalizableTypeEvent::onPreNormalize ],
            [ NormalizableTypeEvent::onPreDenormalize ],
        ];
    }

    public function testNotSetNormalizer(): void
    {
        $type = new TestNormalizableType();
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreNormalize, $type, 1);

        $this->expectException(\RuntimeException::class);
        $type->convertToDatabaseValue(new \stdClass(), $platform);
    }

    public function testNotSetDenormalizer(): void
    {
        $type = new TestNormalizableType();
        $platform = $this->getConfiguredPlatform(NormalizableTypeEvent::onPreDenormalize, $type, 1);

        $this->expectException(\RuntimeException::class);
        $type->convertToPHPValue('[]', $platform);
    }

    public function testGetSQLDeclaration(): void
    {
        $type = new TestNormalizableType();
        $fieldDeclaration = ['type' => 'some type'];
        $return = 'JSON_TYPE';
        $platform = $this->getJsonAwarePlatform(array_merge($fieldDeclaration, ['jsonb' => true]), $return);

        $result = $type->getSQLDeclaration($fieldDeclaration, $platform);

        $this->assertEquals($return, $result);
    }

    private function getJsonAwarePlatform(array $fieldDeclaration, string $return): AbstractPlatform
    {
        $mock = $this->createMock(AbstractPlatform::class);
        $mock->expects($this->once())->method('getJsonTypeDeclarationSQL')->with($fieldDeclaration)->willReturn($return);

        return $mock;
    }
}