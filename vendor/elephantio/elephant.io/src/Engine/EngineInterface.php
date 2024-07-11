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

use Psr\Log\LoggerAwareInterface;

/**
 * Represents an engine used within Elephant.io to send/receive messages from
 * a socket.io server.
 *
 * Loosely based on the work of the following:
 *   - Ludovic Barreca (@ludovicbarreca)
 *   - Mathieu Lallemand (@lalmat)
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
interface EngineInterface extends LoggerAwareInterface
{
    /**
     * Get the name of the engine.
     *
     * @return string
     */
    public function getName();

    /**
     * Connect to the targeted server.
     *
     * @return \ElephantIO\Engine\EngineInterface
     */
    public function connect();

    /**
     * Is connected to server?
     *
     * @return bool
     */
    public function connected();

    /**
     * Disconnect from server.
     *
     * @return \ElephantIO\Engine\EngineInterface
     */
    public function disconnect();

    /**
     * Set socket namespace.
     *
     * @param string $namespace The namespace
     * @return \ElephantIO\Engine\Packet
     */
    public function of($namespace);

    /**
     * Emit an event to server.
     *
     * @param string $event Event to emit
     * @param array $args Arguments to send
     * @param bool $ack Set to true to request an ack
     * @return int|\ElephantIO\Engine\Packet Number of bytes written or acknowledged packet
     */
    public function emit($event, array $args, $ack = null);

    /**
     * Wait for event to arrive.
     *
     * @param string $event
     * @param float $timeout Timeout in seconds
     * @return \ElephantIO\Engine\Packet
     */
    public function wait($event, $timeout = 0);

    /**
     * Drain data from socket.
     *
     * @param float $timeout Timeout in seconds
     * @return \ElephantIO\Engine\Packet
     */
    public function drain($timeout = 0);

    /**
     * Acknowledge a packet.
     *
     * @param \ElephantIO\Engine\Packet $packet Packet to acknowledge
     * @param array $args Acknowledgement data
     * @return int Number of bytes written
     */
    public function ack($packet, array $args);
}
