<?php

declare(strict_types=1);

namespace CuriosityConference\JsonValidator\Validation;

use CuriosityConference\JsonValidator\Validation\Exception\InvalidHttpJsonResponseSchema;
use CuriosityConference\JsonValidator\Validation\Model\JsonString;
use CuriosityConference\JsonValidator\Validation\Model\SchemaId;
use CuriosityConference\JsonValidator\Validation\Model\SchemaPath;
use Opis\JsonSchema\ValidationResult;

interface JsonValidator
{
    /**
     * @throws InvalidHttpJsonResponseSchema
     */
    public function validate(JsonString $jsonString, SchemaPath $schemaPath, SchemaId $schemaId): ValidationResult;
}
