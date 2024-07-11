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

namespace ElephantIO\Engine\Transport;

use ElephantIO\Engine\Transport;
use ElephantIO\Stream\StreamInterface;
use ElephantIO\Util;

/**
 * HTTP polling transport.
 *
 * @author Toha <tohenk@yahoo.com>
 */
class Polling extends Transport
{
    /**
     * @var bool
     */
    protected $chunked = null;

    /**
     * @var array
     */
    protected $result = null;

    /**
     * @var int
     */
    protected $bytesWritten = null;

    /**
     * Get connection default headers.
     *
     * @return array
     */
    protected function getDefaultHeaders()
    {
        return [
            'Connection' => $this->sio->getOptions()->reuse_connection ? 'keep-alive' : 'close',
        ];
    }

    /**
     * Get websocket upgrade headers.
     *
     * @return array
     */
    protected function getUpgradeHeaders()
    {
        $hash = sha1(uniqid(mt_rand(), true), true);
        if ($this->sio->getOptions()->version > 2) {
            $hash = substr($hash, 0, 16);
        }
        $headers = [
            'Upgrade' => 'websocket',
            'Connection' => 'Upgrade',
            'Sec-WebSocket-Key' => base64_encode($hash),
            'Sec-WebSocket-Version' => '13',
            'Origin' => $this->sio->getContext()['headers']['Origin'] ?? '*',
        ];
        if (!empty($this->sio->getCookies())) {
            $headers['Cookie'] = implode('; ', $this->sio->getCookies());
        }

        return $headers;
    }

    /**
     * Perform HTTP request to server.
     *
     * @param string $uri
     * @param array $headers Key-value pairs
     * @param array $options Request options
     * @return bool
     */
    protected function request($uri, $headers = [], $options = [])
    {
        if (!$this->sio->getStream()->available()) {
            return;
        }

        $method = isset($options['method']) ? $options['method'] : 'GET';
        $timeout = isset($options['timeout']) ? $options['timeout'] : 0;
        $skip_body = isset($options['skip_body']) ? $options['skip_body'] : false;
        $payload = isset($options['payload']) ? $options['payload'] : null;

        if ($payload) {
            $contentType = $headers['Content-Type'] ?? null;

            if (null === $contentType) {
                $payload = mb_convert_encoding($payload, 'UTF-8', 'ISO-8859-1');
                $headers = array_merge([
                    'Content-Type' => 'text/plain; charset=UTF-8',
                    'Content-Length' => strlen($payload),
                ], $headers);
            }
        }

        $headers = array_merge(['Host' => $this->sio->getStream()->getUrl()->getHost()], $headers);
        if (isset($this->sio->getOptions()->headers)) {
            $headers = array_merge($headers, $this->sio->getOptions()->headers);
        }
        $request = array_merge([
            sprintf('%s %s HTTP/1.1', strtoupper($method), $uri),
        ], Util::normalizeHeaders($headers));

        $request = implode(StreamInterface::EOL, $request) . StreamInterface::EOL . StreamInterface::EOL . $payload;

        $this->bytesWritten = $this->sio->getStream()->write($request);

        $this->result = ['headers' => [], 'body' => null];

        // wait for response
        $header = true;
        $len = null;
        $start = microtime(true);
        while (true) {
            if ($timeout > 0 && microtime(true) - $start >= $timeout) {
                $this->timedout = true;
                break;
            }
            if (!$this->sio->getStream()->readable()) {
                break;
            }
            if ($content = $this->sio->getStream()->read($header ? null : $len)) {
                if ($content === StreamInterface::EOL && $header && count($this->result['headers'])) {
                    if ($skip_body) {
                        break;
                    }
                    $header = false;
                } else {
                    if ($header) {
                        if ($content = trim($content)) {
                            $this->result['headers'][] = $content;
                            if (null === $len && ($contentLength = $this->getHeaderMatch('Content-Length', $content))) {
                                $len = (int) $contentLength;
                            }
                            if (null === $this->chunked &&
                                ($transferEncoding = $this->getHeaderMatch('Transfer-Encoding', $content)) &&
                                strtolower($transferEncoding) === 'chunked') {
                                $this->chunked = true;
                            }
                        }
                    } else {
                        $this->result['body'] .= $content;
                        if ($this->chunked && null === $len && $content === '0' . StreamInterface::EOL) {
                            $this->result['body'] = $this->decodeChunked($this->result['body']);
                            break;
                        }
                        if ($len === strlen($this->result['body'])) {
                            break;
                        }
                    }
                }
            }
            usleep($this->sio->getOptions()->wait);
        }

        return count($this->result['headers']) ? true : false;
    }

    /**
     * Get response headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return is_array($this->result) ? $this->result['headers'] : null;
    }

    /**
     * Get response body.
     *
     * @return string
     */
    public function getBody()
    {
        return is_array($this->result) ? $this->result['body'] : null;
    }

    /**
     * Get response status.
     *
     * @return string
     */
    public function getStatus()
    {
        if (count($headers = $this->getHeaders())) {
            return $headers[0];
        }
    }

    /**
     * Get response status code.
     *
     * @return string Numeric status code
     */
    public function getStatusCode()
    {
        if (($status = $this->getStatus()) && preg_match('#^HTTP\/\d+\.\d+#', $status)) {
            list(, $code, ) = explode(' ', $status, 3);

            return $code;
        }
    }

    /**
     * Copied from https://stackoverflow.com/questions/10793017/how-to-easily-decode-http-chunked-encoded-string-when-making-raw-http-request
     */
    protected function decodeChunked($str)
    {
        for ($res = ''; !empty($str); $str = trim($str)) {
            $pos = strpos($str, StreamInterface::EOL);
            $len = hexdec(substr($str, 0, $pos));
            $res .= substr($str, $pos + 2, $len);
            $str = substr($str, $pos + 2 + $len);
        }

        return $res;
    }

    /**
     * Get match for header name.
     *
     * @param string $name
     * @param string $header
     * @return string
     */
    public function getHeaderMatch($name, $header)
    {
        $prefix = sprintf('%s:', $name);
        if (0 === stripos($header, $prefix)) {
            return trim(substr($header, strlen($prefix)));
        }
    }

    /** {@inheritDoc} */
    public function send($data, $options = [])
    {
        $_options = ['method' => 'POST', 'payload' => $data];
        $headers = $this->getDefaultHeaders();
        $code = 200;
        $transport = isset($options['transport']) ? $options['transport'] : $this->sio->getOptions()->transport;
        $uri = $this->sio->buildQuery($this->sio->buildQueryParameters($transport));
        $this->request($uri, $headers, $_options);

        if ($this->getStatusCode() == $code) {
            return $this->bytesWritten;
        }
    }

    /** {@inheritDoc} */
    public function recv($timeout = 0, $options = [])
    {
        $this->timedout = null;
        $_options = [];
        if (isset($options['upgrade']) && $options['upgrade']) {
            $headers = $this->getUpgradeHeaders();
            $_options['skip_body'] = true;
            $code = 101;
        } else {
            $headers = $this->getDefaultHeaders();
            $code = 200;
        }
        $transport = isset($options['transport']) ? $options['transport'] : $this->sio->getOptions()->transport;
        $uri = $this->sio->buildQuery($this->sio->buildQueryParameters($transport));
        $this->request($uri, $headers, $_options);

        if ($this->getStatusCode() == $code) {
            return null !== $this->getBody() ? $this->getBody() : '';
        }
    }
}
