<?php

/*
 * This file is part of the PHP to JSON Schema package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\PhpToJsonSchema\Tests\Fixtures;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class Dummy
{
    /**
     * The foo var.
     *
     * @var string
     */
    public $foo;

    /**
     * Should not be in the schema (read only).
     */
    public function getBar()
    {
    }

    public function setBaz(float $baz)
    {
    }
}
