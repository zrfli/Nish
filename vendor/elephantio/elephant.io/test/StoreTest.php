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

use PHPUnit\Framework\TestCase;
use ElephantIO\Engine\Store;
use InvalidArgumentException;

class StoreTest extends TestCase
{
    public function testStore()
    {
        $store = TestStore::create([
            'id' => 'ID',
            'number' => 9,
            'disp' => 'disp',
            'nodisp' => 'nodisp',
            'hidden' => 'hidden',
        ]);

        $this->assertSame("ID{\"number\":9,\"disp\":\"disp\"}", (string) $store, 'Properly cast store to string');
        $store->id = 'the-id';
        $this->assertEquals('the-id', $store->id, 'Store can retrieve the value by its key');

        try {
            $store->somekey = 'nothing';
            $this->fail('Setting non existent key did not throw an exception!');
        } catch (InvalidArgumentException $e) {
        }
    }
}

class TestStore extends Store
{
    protected function initialize()
    {
        $this->keys = ['+id', 'number', '!disp', '!nodisp', '_hidden'];
    }
}