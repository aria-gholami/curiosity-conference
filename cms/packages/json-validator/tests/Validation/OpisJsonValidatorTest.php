<?php

declare(strict_types=1);

namespace CuriosityConference\JsonValidator\Tests\Validation;

use CuriosityConference\JsonValidator\Validation\Exception\InvalidHttpJsonResponseSchema;
use CuriosityConference\JsonValidator\Validation\Model\JsonString;
use CuriosityConference\JsonValidator\Validation\Model\SchemaId;
use CuriosityConference\JsonValidator\Validation\Model\SchemaPath;
use CuriosityConference\JsonValidator\Validation\OpisJsonValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OpisJsonValidator::class)]
#[UsesClass(JsonString::class)]
final class OpisJsonValidatorTest extends TestCase
{
    private OpisJsonValidator $opisJsonValidator;

    protected function setUp(): void
    {
        $this->opisJsonValidator = new OpisJsonValidator();
    }

    public function testValidateIsValid(): void
    {
        $jsonString = new JsonString('{"test":"bar"}');
        $schemaPath = new SchemaPath(__DIR__ . '/Schema/test.json');
        $schemaId = new SchemaId('https://test.com/test.json');

        $validationResult = $this->opisJsonValidator->validate($jsonString, $schemaPath, $schemaId);

        self::assertTrue($validationResult->isValid());
    }

    public function testValidateWithInvalidJsonResponse(): void
    {
        $jsonString = new JsonString('{"test":"bar}');
        $schemaPath = new SchemaPath(__DIR__ . '/Schema/test.json');
        $schemaId = new SchemaId('https://test.com/test.json');

        try {
            $this->opisJsonValidator->validate($jsonString, $schemaPath, $schemaId);
            $this->fail('InvalidHttpJsonResponseSchema was expected to be thrown.');
        } catch (InvalidHttpJsonResponseSchema $exception) {
            self::assertSame(
                'Validation failed for value "jsonString" with error: "Json schema validator component "jsonString" is not created successfully."',
                $exception->getMessage(),
            );
        }
    }
}
