<?php

/**
 * This file is part of the Elephant.io package
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 *
 * @copyright Wisembly
 * @license   http://www.opensource.org/licenses/MIT-License MIT License
 */

namespace ElephantIO\Test;

use ElephantIO\Payload as BasePayload;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

class PayloadTest extends TestCase
{
    public function testMaskData()
    {
        $payload = new Payload();

        $refl = new ReflectionProperty('ElephantIO\\Test\\Payload', 'maskKey');
        $refl->setAccessible(true);
        $refl->setValue($payload, '?EV!');

        $refl = new ReflectionMethod('ElephantIO\\Test\\Payload', 'maskData');
        $refl->setAccessible(true);

        $this->assertSame('592a39', bin2hex($refl->invoke($payload, 'foo')));
    }
}

/** Fixtures for these tests */
class Payload extends BasePayload
{
}
