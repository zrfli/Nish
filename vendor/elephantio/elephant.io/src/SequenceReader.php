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

namespace ElephantIO;

/**
 * A sequence data reader.
 *
 * @author Toha <tohenk@yahoo.com>
 */
class SequenceReader
{
    /**
     * @var string
     */
    protected $data = null;

    /**
     * Constructor.
     *
     * @param string $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Read a fixed size data or remaining data if size is null.
     *
     * @param int $size
     * @return string
     */
    public function read($size = 1)
    {
        if (!$this->isEof()) {
            if (null === $size) {
                $result = $this->data;
                $this->data = '';
            } else {
                $result = substr($this->data, 0, $size);
                $this->data = substr($this->data, $size);
            }

            return $result;
        }
    }

    /**
     * Read data up to delimiter.
     *
     * @param string $delimiter
     * @param array $noskips
     * @return string
     */
    public function readUntil($delimiter = ',', $noskips = [])
    {
        if (!$this->isEof()) {
            list($p, $d) = $this->getPos($this->data, $delimiter);
            if (false !== $p) {
                $result = substr($this->data, 0, $p);
                // skip delimiter
                if (!in_array($d, $noskips)) {
                    $p++;
                }
                $this->data = substr($this->data, $p);

                return $result;
            }
        }
    }

    /**
     * Get first position of delimiters.
     *
     * @param string $data
     * @param string $delimiter
     * @return boolean|number
     */
    protected function getPos($data, $delimiter)
    {
        $pos = false;
        $delim = null;
        for ($i = 0; $i < strlen($delimiter); $i++) {
            $d = substr($delimiter, $i, 1);
            if (false !== ($p = strpos($data, $d))) {
                if (false === $pos || $p < $pos) {
                    $pos = $p;
                    $delim = $d;
                }
            }
        }

        return [$pos, $delim];
    }

    /**
     * Get unprocessed data.
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Is EOF.
     *
     * @return boolean
     */
    public function isEof()
    {
        return 0 === strlen($this->data) ? true : false;
    }
}
