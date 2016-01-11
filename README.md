# PHP to JSON Schema

Creates a JSON Schema from a PHP entity. Useful to ensure that a given JSON document will
be deserialized properly in an object graph.

## Installation

Use [https://getcomposer.org](Composer) to install the library:

```
composer require dunglas/php-to-json-schema
```

## Usage

```php
use Dunglas\PhpToJsonSchema\Generator;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

class MyClass
{
    private $foo;
    private $bar;

    public function setFoo(string $foo)
    {
        $this->foo = $foo;
    }

    public function setBar(float $bar = null)
    {
        $this->bar = $bar;
    }

    // ...
}


$reflectionExtractor = new ReflectionExtractor();
$propertyInfoExtractor = new PropertyInfoExtractor([$reflectionExtractor], [$reflectionExtractor], [], [$reflectionExtractor]);

$this->generator = new Generator($propertyInfoExtractor);
echo json_encode($generator->generate(MyClass::class));
```

## Credits

Created by [Kévin Dunglas](https://dunglas.fr).
