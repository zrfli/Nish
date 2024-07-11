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

namespace ElephantIO\Engine;

use Psr\Log\LoggerAwareTrait;

/**
 * Socket transport.
 *
 * @author Toha <tohenk@yahoo.com>
 */
abstract class Transport
{
    use LoggerAwareTrait;

    /**
     * Socket interface.
     *
     * @var \ElephantIO\Engine\SocketInterface
     */
    protected $sio;

    /**
     * Last operation timed out state.
     *
     * @var bool
     */
    protected $timedout;

    /**
     * Constructor.
     *
     * @param \ElephantIO\Engine\SocketInterface $sio
     */
    public function __construct($sio)
    {
        $this->sio = $sio;
    }

    /**
     * Send data.
     *
     * @param string $data
     * @param array $options
     * @return int Number of byte written
     */
    abstract public function send($data, $options = []);

    /**
     * Receive data.
     *
     * @param int $timeout
     * @param array $options
     * @return string
     */
    abstract public function recv($timeout = 0, $options = []);

    /**
     * Is last operation timed out?
     *
     * @return bool
     */
    public function timedout()
    {
        return $this->timedout;
    }
}
