<?php

declare(strict_types=1);

namespace CuriosityConference\ApiAdapter\Tests\Http\Mocks;

use CuriosityConference\JsonValidator\Validation\JsonValidator;
use CuriosityConference\JsonValidator\Validation\Model\JsonString;
use CuriosityConference\JsonValidator\Validation\Model\SchemaId;
use CuriosityConference\JsonValidator\Validation\Model\SchemaPath;
use Opis\JsonSchema\ValidationResult;

final class JsonValidatorMock implements JsonValidator
{
    public JsonString $inputJsonString;
    public SchemaPath $inputSchemaPath;
    public SchemaId $inputSchemaId;
    public ValidationResult $outputValidationResult;

    public function validate(JsonString $jsonString, SchemaPath $schemaPath, SchemaId $schemaId): ValidationResult
    {
        $this->inputJsonString = $jsonString;
        $this->inputSchemaPath = $schemaPath;
        $this->inputSchemaId = $schemaId;

        return $this->outputValidationResult;
    }
}
