<?php

/*
 * This file is part of the PHP to JSON Schema package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\PhpToJsonSchema;

use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * Generates a JSON Schema from a PHP class.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class Generator
{
    private $propertyInfoExtractor;

    public function __construct(PropertyInfoExtractorInterface $propertyInfoExtractor)
    {
        $this->propertyInfoExtractor = $propertyInfoExtractor;
    }

    /**
     * Generates an associative array to pass to `json_encode` representing
     * the JSON Schema corresponding to the given class.
     *
     * @param string $className
     * @param array  $context
     *
     * @return array
     */
    public function generate(string $className, array $context = []) : array
    {
        $schema = [
            'title' => $context['title'] ?? (new \ReflectionClass($className))->getShortName(),
            'type' => 'object',
        ];

        $phpProperties = $this->propertyInfoExtractor->getProperties($className, $context['property_info_context'] ?? []);

        if (empty($phpProperties)) {
            return $schema;
        }

        $required = [];

        foreach ($phpProperties as $propertyName) {
            $propertyInfoContext = $context['property_info_context'] ?? [];

            $writable = $this->propertyInfoExtractor->isWritable($className, $propertyName, $propertyInfoContext);

            if (null === $writable || false === $writable) {
                continue;
            }

            $types = $this->propertyInfoExtractor->getTypes($className, $propertyName, $propertyInfoContext);
            if (empty($types)) {
                continue;
            }

            $propertySchema = [];
            $type = $types[0];
            $builtinType = $type->getBuiltinType();

            // Types not supported by JSON
            if (Type::BUILTIN_TYPE_RESOURCE === $builtinType || Type::BUILTIN_TYPE_CALLABLE === $builtinType || Type::BUILTIN_TYPE_NULL === $builtinType) {
                continue;
            }

            if (!$type->isNullable()) {
                $required[] = $propertyName;
            }

            if ($type->isCollection()) {
                // TODO: handle recursion
                $propertySchema['type'] = 'array';
            } else {
                $propertySchema['type'] = $this->convertTypeName($builtinType);
            }

            if (Type::BUILTIN_TYPE_OBJECT === $builtinType) {
                $propertySchema['$ref'] = sprintf('%s.json', str_replace('\\', '-', $type->getClassName()));
            }

            $description = $this->propertyInfoExtractor->getShortDescription($className, $propertyName, $propertyInfoContext);
            if (!empty($description)) {
                $propertySchema['description'] = $description;
            }

            $schema['properties'][$propertyName] = $propertySchema;
        }

        if (!empty($required)) {
            $schema['required'] = $required;
        }

        return $schema;
    }

    private function convertTypeName(string $type) : string
    {
        switch ($type) {
            case Type::BUILTIN_TYPE_BOOL:
                return 'boolean';

            case Type::BUILTIN_TYPE_INT:
                return 'integer';
        }

        return $type;
    }
}
