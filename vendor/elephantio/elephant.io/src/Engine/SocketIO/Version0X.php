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

namespace ElephantIO\Engine\SocketIO;

use ElephantIO\Engine\SocketIO;
use ElephantIO\Exception\ServerConnectionFailureException;
use ElephantIO\SequenceReader;
use ElephantIO\Util;
use InvalidArgumentException;
use RuntimeException;

/**
 * Implements the dialog with socket.io server 0.x.
 *
 * Based on the work of Baptiste Clavié (@Taluu)
 *
 * @auto ByeoungWook Kim <quddnr145@gmail.com>
 * @link https://tools.ietf.org/html/rfc6455#section-5.2 Websocket's RFC
 */
class Version0X extends SocketIO
{
    public const PROTO_DISCONNECT = 0;
    public const PROTO_CONNECT = 1;
    public const PROTO_HEARTBEAT = 2;
    public const PROTO_MESSAGE = 3;
    public const PROTO_JSON = 4;
    public const PROTO_EVENT = 5;
    public const PROTO_ACK = 6;
    public const PROTO_ERROR = 7;
    public const PROTO_NOOP = 8;

    public const SEPARATOR = "\u{fffd}";

    /** @var int */
    private $ackId = null;

    /** {@inheritDoc} */
    public function getName()
    {
        return 'SocketIO Version 0.X';
    }

    /** {@inheritDoc} */
    protected function getDefaultOptions()
    {
        return [
            'version' => 1,
        ];
    }

    /** {@inheritDoc} */
    protected function processData($data)
    {
        /** @var \ElephantIO\Engine\Packet $result */
        $result = null;
        if ($this->transport === static::TRANSPORT_POLLING && false !== strpos($data, static::SEPARATOR)) {
            $packets = explode(static::SEPARATOR, trim($data, static::SEPARATOR));
        } else {
            $packets = [$data];
        }
        $more = count($packets) > 1;
        while (count($packets)) {
            // skip length line if multiple packets found
            if ($more) {
                array_shift($packets);
            }
            $data = array_shift($packets);
            $this->logger->debug(sprintf('Processing data: %s', $data));
            $packet = $this->decodePacket($data);
            switch ($packet->proto) {
                case static::PROTO_DISCONNECT:
                    $this->logger->debug('Connection closed by server');
                    $this->reset();
                    throw new RuntimeException('Connection closed by server!');
                case static::PROTO_HEARTBEAT:
                    $this->logger->debug('Got HEARTBEAT');
                    $this->send(static::PROTO_HEARTBEAT);
                    break;
                case static::PROTO_NOOP:
                    break;
                default:
                    if (null === $result) {
                        $result = $packet;
                    } else {
                        $result->add($packet);
                    }
                    break;
            }
        }

        return $result;
    }

    /** {@inheritDoc} */
    protected function matchEvent($packet, $event)
    {
        if (($found = $packet->peek(static::PROTO_EVENT)) && $this->matchNamespace($found->nsp) && $found->event === $event) {
            return $found;
        }
    }

    /** {@inheritDoc} */
    protected function createEvent($event, $args, $ack = null)
    {
        $this->ackId = $ack ? $this->getAckId(true) : null;

        return [static::PROTO_EVENT, json_encode(['name' => $event, 'args' => $this->replaceResources($args)]), null];
    }

    /** {@inheritDoc} */
    protected function matchAck($packet)
    {
        if (($found = $packet->peek(static::PROTO_ACK)) &&
            $this->matchNamespace($found->nsp) &&
            $found->ack == $this->getAckId()) {
            return $found;
        }
    }

    /** {@inheritDoc} */
    protected function createAck($packet, $data)
    {
        return [static::PROTO_ACK, implode('+', [$packet->ack, json_encode($data)])];
    }

    /** {@inheritDoc} */
    protected function getPacketMaps()
    {
        return [
            'proto' => [
                static::PROTO_DISCONNECT => 'disconnect',
                static::PROTO_CONNECT => 'connect',
                static::PROTO_HEARTBEAT => 'heartbeat',
                static::PROTO_MESSAGE => 'message',
                static::PROTO_JSON => 'json',
                static::PROTO_EVENT => 'event',
                static::PROTO_ACK => 'ack',
                static::PROTO_ERROR => 'error',
                static::PROTO_NOOP => 'noop',
            ]
        ];
    }

    /**
     * Decode a packet.
     *
     * @param string $data
     * @return \ElephantIO\Engine\Packet
     */
    protected function decodePacket($data)
    {
        $seq = new SequenceReader($data);
        $proto = $seq->readUntil(':');
        if (null === $proto && is_numeric($seq->getData())) {
            $proto = $seq->getData();
        }
        $proto = (int) $proto;
        if ($proto >= static::PROTO_DISCONNECT && $proto <= static::PROTO_NOOP) {
            $packet = $this->createPacket($proto);
            if ($ack = $seq->readUntil(':')) {
                if ('+' === substr($ack, -1)) {
                    $ack = substr($ack, 0, -1);
                }
                $packet->ack = $ack;
            }
            if (null === ($nsp = $seq->readUntil(':')) && !$seq->isEof()) {
                $nsp = $seq->read(null);
            }
            $packet->nsp = $nsp;
            $packet->data = !$seq->isEof() ? $seq->getData() : null;
            switch ($packet->proto) {
                case static::PROTO_JSON:
                    if ($packet->data) {
                        $packet->data = json_decode($packet->data, true);
                    }
                    break;
                case static::PROTO_EVENT:
                    if ($packet->data) {
                        $data = json_decode($packet->data, true);
                        $this->replaceBuffers($data['args']);
                        $packet->event = $data['name'];
                        $packet->setArgs($data['args']);
                    }
                    break;
                case static::PROTO_ACK:
                    list($ack, $data) = explode('+', $packet->data, 2);
                    $packet->ack = $ack;
                    $packet->setArgs(json_decode($data, true));
                    break;
            }
            $this->logger->info(sprintf('Got packet: %s', Util::truncate((string) $packet)));

            return $packet;
        }
    }

    /**
     * Replace arguments with resource content.
     *
     * @param array $array
     * @return array
     */
    protected function replaceResources($array)
    {
        if (is_array($array)) {
            foreach ($array as &$value) {
                if (is_resource($value)) {
                    fseek($value, 0);
                    if ($content = stream_get_contents($value)) {
                        $value = $content;
                    } else {
                        $value = null;
                    }
                }
                if (is_array($value)) {
                    $value = $this->replaceResources($value);
                }
            }
        }

        return $array;
    }

    /**
     * Replace returned buffer content.
     *
     * @param array $array
     */
    protected function replaceBuffers(&$array)
    {
        if (is_array($array)) {
            foreach ($array as &$value) {
                if (is_array($value) && isset($value['type']) && isset($value['data'])) {
                    if ($value['type'] === 'Buffer') {
                        $value = implode(array_map('chr', $value['data']));
                        if ($this->options->binary_as_resource) {
                            $value = Util::toResource($value);
                        }
                    }
                }
                if (is_array($value)) {
                    $this->replaceBuffers($value);
                }
            }
        }
    }

    protected function isProtocol($proto)
    {
        if ($proto < static::PROTO_DISCONNECT || $proto > static::PROTO_NOOP) {
            throw new InvalidArgumentException('Wrong protocol type to sent to server');
        }

        return true;
    }

    protected function formatProtocol($proto, $data = null)
    {
        $items = [$proto, $proto === static::PROTO_EVENT && null !== $this->ackId ? $this->ackId . '+' : '', $this->namespace];
        if (null !== $data) {
            $items[] = $data;
        }

        return implode(':', $items);
    }

    public function buildQueryParameters($transport)
    {
        $transports = [static::TRANSPORT_POLLING => 'xhr-polling'];
        $transport = $transport ?? $this->options->transport;
        if (isset($transports[$transport])) {
            $transport = $transports[$transport];
        }
        $path = [$this->options->version, $transport];
        if ($this->session) {
            $path[] = $this->session->id;
        }

        return ['path' => $path];
    }

    public function buildQuery($query)
    {
        $url = $this->stream->getUrl()->getParsed();
        $uri = sprintf('/%s/%s', trim($url['path'], '/'), implode('/', $query['path']));
        if (isset($url['query']) && $params = http_build_query($url['query'])) {
            $uri .= '/?' . $params;
        }

        return $uri;
    }

    protected function doHandshake()
    {
        if (null !== $this->session) {
            return;
        }

        $this->logger->info('Starting handshake');

        // set timeout to default
        $this->setTimeout($this->defaults['timeout']);

        /** @var \ElephantIO\Engine\Transport\Polling $transport */
        $transport = $this->_transport();
        if (null === ($data = $transport->recv())) {
            throw new ServerConnectionFailureException('unable to perform handshake');
        }

        $sess = explode(':', $data);
        $handshake = [
            'sid' => $sess[0],
            'pingInterval' => (int) $sess[1],
            'pingTimeout' => (int) $sess[2],
            'upgrades' => explode(',', $sess[3]),
        ];
        $this->storeSession($handshake, $transport->getHeaders());

        $this->logger->info(sprintf('Handshake finished with %s', (string) $this->session));
    }

    protected function doUpgrade()
    {
        $this->logger->info('Starting websocket upgrade');

        // set timeout based on handshake response
        $this->setTimeout($this->session->getTimeout());

        if (null !== $this->_transport()->recv(0, ['transport' => static::TRANSPORT_WEBSOCKET, 'upgrade' => true])) {
            $this->setTransport(static::TRANSPORT_WEBSOCKET);
            $this->stream->upgrade();

            $this->logger->info('Websocket upgrade completed');
        } else {
            $this->logger->info('Upgrade failed, skipping websocket');
        }
    }

    protected function doSkipUpgrade()
    {
        // send get request to setup connection
        $this->_transport()->recv();
    }

    protected function doChangeNamespace()
    {
        $this->send(static::PROTO_CONNECT);

        $packet = $this->drain();
        if ($packet && $packet->proto === static::PROTO_CONNECT) {
            $this->logger->debug('Successfully connected');
        }

        return $packet;
    }

    protected function doClose()
    {
        $this->send(static::PROTO_DISCONNECT);
    }
}
