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

use ElephantIO\Engine\Packet;
use ElephantIO\Engine\Transport\Polling;
use ElephantIO\Engine\Transport\Websocket;
use ElephantIO\Exception\SocketException;
use ElephantIO\Exception\UnsupportedActionException;
use ElephantIO\Stream\Stream;
use ElephantIO\Util;
use InvalidArgumentException;
use Psr\Log\LoggerAwareTrait;

/**
 * Elephant.io socket engine base class.
 */
abstract class SocketIO implements EngineInterface, SocketInterface
{
    use LoggerAwareTrait;

    public const TRANSPORT_POLLING = 'polling';
    public const TRANSPORT_WEBSOCKET = 'websocket';

    /** @var string[] Parsed url */
    protected $url;

    /** @var string Normalized namespace without path prefix */
    protected $namespace = '';

    /** @var \ElephantIO\Engine\Session Session information */
    protected $session;

    /** @var array Cookies received during handshake */
    protected $cookies = [];

    /** @var \ElephantIO\Engine\Option Array of options for the engine */
    protected $options;

    /** @var \ElephantIO\Stream\StreamInterface Resource to the connected stream */
    protected $stream;

    /** @var string Current socket transport */
    protected $transport = null;

    /** @var mixed[] Array of php stream context options */
    protected $context = [];

    /** @var mixed[] Array of default options for the engine */
    protected $defaults;

    /** @var \ElephantIO\Engine\Transport */
    private $_transport = null;

    /** @var int Acknowledgement id */
    private static $ack = null;

    public function __construct($url, array $options = [])
    {
        $this->url = $url;

        if (isset($options['headers'])) {
            Util::handleDeprecatedHeaderOptions($options['headers']);
        }

        if (isset($options['context']['headers'])) {
            Util::handleDeprecatedHeaderOptions($options['context']['headers']);
        }

        if (isset($options['context'])) {
            $this->context = $options['context'];
            unset($options['context']);
        }

        $this->defaults = array_merge([
            'wait' => 50,
            'timeout' => ini_get('default_socket_timeout'),
            'reuse_connection' => true,
            'transport' => static::TRANSPORT_POLLING,
            'transports' => null,
            'binary_as_resource' => true,
        ], $this->getDefaultOptions());

        $this->options = Option::create(array_replace($this->defaults, $options));
    }

    /** {@inheritDoc} */
    public function getName()
    {
        return 'SocketIO';
    }

    /**
     * Get options.
     *
     * @return \ElephantIO\Engine\Option
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get underlying socket stream.
     *
     * @return \ElephantIO\Stream\StreamInterface
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Get session.
     *
     * @return \ElephantIO\Engine\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Get cookies.
     *
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Get stream context.
     *
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Get current socket transport.
     *
     * @return string
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * Set current socket transport.
     *
     * @param string $transport Socket transport name
     * @return \ElephantIO\Engine\SocketIO
     */
    public function setTransport($transport)
    {
        if (!in_array($transport, $this->getTransports())) {
            throw new InvalidArgumentException(sprintf('Unsupported transport "%s"!', $transport));
        }
        $this->transport = $transport;

        return $this;
    }

    /**
     * Get current transport.
     *
     * @return \ElephantIO\Engine\Transport
     */
    protected function _transport()
    {
        if (null === $this->_transport || $this->stream->wasUpgraded()) {
            $this->createStream();

            switch ($this->stream->upgraded()) {
                case true:
                    $this->_transport = new Websocket($this);
                    break;
                default:
                    $this->_transport = new Polling($this);
                    break;
            }
            $this->_transport->setLogger($this->logger);
        }

        return $this->_transport;
    }

    /** {@inheritDoc} */
    public function connect()
    {
        if ($this->connected()) {
            return;
        }

        $this->setTransport($this->options->transport);
        $this->doHandshake();
        $this->doAfterHandshake();
        if ($this->isUpgradable()) {
            $this->doUpgrade();
        } else {
            $this->doSkipUpgrade();
        }
    }

    /** {@inheritDoc} */
    public function connected()
    {
        return $this->stream ? $this->stream->readable() : false;
    }

    /** {@inheritDoc} */
    public function disconnect()
    {
        if (!$this->connected()) {
            return;
        }

        if ($this->session) {
            $this->doClose();
        }
        $this->reset();
    }

    /** {@inheritDoc} */
    public function of($namespace)
    {
        $normalized = Util::normalizeNamespace($namespace);
        if ($this->namespace !== $normalized) {
            $this->namespace = $normalized;

            return $this->doChangeNamespace();
        }
    }

    /** {@inheritDoc} */
    public function emit($event, array $args, $ack = null)
    {
        list($proto, $data, $raws) = $this->createEvent($event, $args, $ack);

        $len = $this->send($proto, $data);
        if (is_array($raws)) {
            foreach ($raws as $raw) {
                $len += $this->_transport()->send($raw);
            }
        }

        // wait for an ack
        if ($ack) {
            return $this->waitForPacket(function($packet) {
                return $this->matchAck($packet);
            });
        }

        return $len;
    }

    /** {@inheritDoc} */
    public function wait($event, $timeout = 0)
    {
        return $this->waitForPacket(function($packet) use ($event) {
            return $this->matchEvent($packet, $event);
        }, $timeout);
    }

    /** {@inheritDoc} */
    public function drain($timeout = 0)
    {
        if (null !== ($data = $this->_transport()->recv($timeout))) {
            $this->logger->debug(sprintf('Got data: %s', Util::truncate((string) $data)));

            return $this->processData($data);
        }
    }

    /** {@inheritDoc} */
    public function ack($packet, array $args)
    {
        list($proto, $data) = $this->createAck($packet, $args);

        return $this->send($proto, $data);
    }

    /**
     * Send protocol and its data to server.
     *
     * @param int $proto Protocol type
     * @param string  $data Optional data to be sent
     * @return int Number of bytes written
     */
    public function send($proto, $data = null)
    {
        if ($this->isProtocol($proto)) {
            $formatted = $this->formatProtocol($proto, $data);
            $this->logger->debug(sprintf('Send data: %s', Util::truncate($formatted)));

            return $this->_transport()->send($formatted);
        }
    }

    /**
     * Get the defaults options.
     *
     * @return array Defaults options for this engine
     */
    protected function getDefaultOptions()
    {
        return [];
    }

    /**
     * Wait for a matched packet to arrive.
     *
     * @param callable $matcher
     * @param float $timeout
     * @return \ElephantIO\Engine\Packet
     */
    protected function waitForPacket($matcher, $timeout = 0)
    {
        while (true) {
            if ($packet = $this->drain($timeout)) {
                if ($match = $matcher($packet)) {
                    return $match;
                }
                foreach ($packet->flatten() as $p) {
                    $this->logger->info(sprintf('Ignoring packet: %s', Util::truncate((string) $p)));
                }
            }
            if ($this->_transport()->timedout()) {
                break;
            }
        }
    }

    /**
     * Process received data.
     *
     * @param string $data
     * @return \ElephantIO\Engine\Packet
     */
    protected function processData($data)
    {
        throw new UnsupportedActionException($this, 'processData');
    }

    /**
     * Is namespace match?
     *
     * @param string $namespace
     * @return bool
     */
    protected function matchNamespace($namespace)
    {
        if ($namespace === $this->namespace || Util::normalizeNamespace($namespace) === $this->namespace) {
            return true;
        }
    }

    /**
     * Create an event to sent to server.
     *
     * @param string $event
     * @param array $args
     * @param bool $ack
     * @return array[int, string]
     */
    protected function createEvent($event, $args, $ack = null)
    {
        throw new UnsupportedActionException($this, 'createEvent');
    }

    /**
     * Find matched event from packet.
     *
     * @param \ElephantIO\Engine\Packet $packet
     * @param string $event
     * @return \ElephantIO\Engine\Packet
     */
    protected function matchEvent($packet, $event)
    {
        throw new UnsupportedActionException($this, 'matchEvent');
    }

    /**
     * Create an acknowledgement.
     *
     * @param \ElephantIO\Engine\Packet $packet Packet to acknowledfe
     * @param array $data Acknowledgement data
     * @return array[int, string]
     */
    protected function createAck($packet, $data)
    {
        throw new UnsupportedActionException($this, 'createAck');
    }

    /**
     * Find matched ack from packet.
     *
     * @param \ElephantIO\Engine\Packet $packet
     * @return \ElephantIO\Engine\Packet
     */
    protected function matchAck($packet)
    {
        throw new UnsupportedActionException($this, 'matchAck');
    }

    /**
     * Get or generate acknowledgement id.
     *
     * @param bool $generate
     * @return int
     */
    protected function getAckId($generate = null)
    {
        if ($generate) {
            if (null === self::$ack) {
                self::$ack = 0;
            } else {
                self::$ack++;
            }
        }

        return self::$ack;
    }

    /**
     * Create a packet.
     *
     * @param int $proto
     * @return \ElephantIO\Engine\Packet
     */
    protected function createPacket($proto)
    {
        $packet = new Packet();
        $packet->proto = $proto;
        if (count($maps = $this->getPacketMaps())) {
            $packet->setMaps($maps);
        }

        return $packet;
    }

    /**
     * Get packet value mapping.
     *
     * @return array
     */
    protected function getPacketMaps()
    {
        return [];
    }

    /**
     * Store successful connection handshake as session.
     *
     * @param array $handshake
     * @param array $headers
     */
    protected function storeSession($handshake, $headers = [])
    {
        $cookies = [];
        if (is_array($headers) && count($headers)) {
            foreach ($headers as $header) {
                $matches = null;
                if (preg_match('/^Set-Cookie:\s*([^;]*)/i', $header, $matches)) {
                    $cookies[] = $matches[1];
                }
            }
        }
        $this->cookies = $cookies;
        $this->session = Session::from($handshake);
    }

    /**
     * Create socket stream.
     *
     * @throws \ElephantIO\Exception\SocketException
     */
    protected function createStream()
    {
        if ($this->stream && !$this->options->reuse_connection) {
            $this->logger->debug('Closing socket connection');
            $this->stream->close();
            $this->stream = null;
        }
        if (!$this->stream) {
            $this->stream = Stream::create($this->url, $this->context,
                array_merge($this->options->toArray(), ['logger' => $this->logger]));
            if ($errors = $this->stream->getErrors()) {
                throw new SocketException($errors[0], $errors[1]);
            }
        }
    }

    /**
     * Update or set connection timeout.
     *
     * @param int $timeout
     * @return \ElephantIO\Engine\SocketIO
     */
    protected function setTimeout($timeout)
    {
        $this->options->timeout = $timeout;
        // stream already established?
        if ($this->options->reuse_connection && $this->stream) {
            $this->stream->setTimeout($timeout);
        }

        return $this;
    }

    /**
     * Check if socket transport is enabled.
     *
     * @param string $transport
     * @return bool
     */
    protected function isTransportEnabled($transport)
    {
        $transports = $this->options->transports;

        return
            null === $transports ||
            $transport === $transports ||
            (is_array($transports) && in_array($transport, $transports)) ? true : false;
    }

    /**
     * Get supported socket transports.
     *
     * @return string[]
     */
    protected function getTransports()
    {
        return [static::TRANSPORT_POLLING, static::TRANSPORT_WEBSOCKET];
    }

    /**
     * Build query string parameters.
     *
     * @param string $transport
     * @return array
     */
    public function buildQueryParameters($transport)
    {
        return [];
    }

    /**
     * Build query from parameters.
     *
     * @param array $query
     * @return string
     */
    public function buildQuery($query)
    {
        throw new UnsupportedActionException($this, 'buildQuery');
    }

    /**
     * Do reset.
     */
    protected function reset()
    {
        if ($this->stream) {
            $this->stream->close();
            $this->stream = null;
            $this->session = null;
            $this->cookies = [];
            $this->_transport = null;
        }
    }

    /**
     * Check if protocol is valid.
     *
     * @return bool True if it is a valid protocol
     */
    protected function isProtocol($proto)
    {
        throw new UnsupportedActionException($this, 'isProtocol');
    }

    /**
     * Format data for protocol.
     *
     * @param int $proto
     * @param string $data
     * @return string
     */
    protected function formatProtocol($proto, $data = null)
    {
        throw new UnsupportedActionException($this, 'formatProtocol');
    }

    /**
     * Is transport can be upgraded to websocket?
     *
     * @return bool
     */
    protected function isUpgradable()
    {
        return in_array(static::TRANSPORT_WEBSOCKET, $this->session->upgrades) &&
            $this->isTransportEnabled(static::TRANSPORT_WEBSOCKET) ? true : false;
    }

    protected function doHandshake()
    {
    }

    protected function doAfterHandshake()
    {
    }

    protected function doUpgrade()
    {
    }

    protected function doSkipUpgrade()
    {
    }

    protected function doChangeNamespace()
    {
    }

    protected function doClose()
    {
    }
}
