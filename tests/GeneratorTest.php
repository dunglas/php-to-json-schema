<?php

/*
 * This file is part of the PHP to JSON Schema package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\PhpToJsonSchema\tests;

use Dunglas\PhpToJsonSchema\Generator;
use Dunglas\PhpToJsonSchema\Tests\Fixtures\Dummy;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Generator
     */
    private $generator;

    public function setUp()
    {
        $reflectionExtractor = new ReflectionExtractor();
        $phpDocExtractor = new PhpDocExtractor();

        $extractors = [
            [$reflectionExtractor],
            [$reflectionExtractor],
            [$phpDocExtractor],
            [$reflectionExtractor],
        ];

        $propertyInfoExtractor = new PropertyInfoExtractor(...$extractors);

        $this->generator = new Generator($propertyInfoExtractor);
    }

    public function testGenerate()
    {
        echo json_encode($this->generator->generate(Dummy::class), JSON_PRETTY_PRINT);
        die;
    }
}
