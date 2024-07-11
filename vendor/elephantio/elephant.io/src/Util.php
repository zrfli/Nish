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
 * A collection of utilities.
 *
 * @author Toha <tohenk@yahoo.com>
 */
class Util
{
    /**
     * Normalize namespace.
     *
     * @param string $namespace
     * @return string
     */
    public static function normalizeNamespace($namespace)
    {
        if ($namespace && substr($namespace, 0, 1) === '/') {
            $namespace = substr($namespace, 1);
        }

        return $namespace;
    }

    /**
     * Concatenate namespace with data using separator.
     *
     * @param string $namespace
     * @param string $data
     * @param bool $prefix
     * @param string $separator
     * @return string
     */
    public static function concatNamespace($namespace, $data, $prefix = true, $separator = ',')
    {
        if ($namespace) {
            if ($prefix && substr($namespace, 0, 1) !== '/') {
                $namespace = '/' . $namespace;
            }
            if ($data) {
                $namespace .= $separator;
            }
        }

        return $namespace . $data;
    }

    /**
     * Normalize request headers from key-value pair array.
     *
     * @param array $headers
     * @return array
     */
    public static function normalizeHeaders($headers)
    {
        return array_map(
            function($key, $value) {
                return "$key: $value";
            },
            array_keys($headers),
            $headers
        );
    }

    /**
     * Handles deprecated header options in an array.
     *
     * This function checks the format of the provided array of headers. If the headers are in the old
     * non-associative format (numeric indexed), it triggers a deprecated warning and converts them
     * to the new key-value array format.
     *
     * @param array $headers A reference to the array of HTTP headers to be processed. This array may
     *                      be modified if the headers are in the deprecated format.
     *
     * @return void This function modifies the input array in place and does not return any value.
     */
    public static function handleDeprecatedHeaderOptions(&$headers)
    {
        if (is_array($headers) && count($headers) > 0) {
            // Check if the array is not associative (indicating old format)
            if (array_values($headers) == $headers) {
                trigger_error('You are using a deprecated header format. Please update to the new key-value array format.', E_USER_DEPRECATED);
                $newHeaders = [];
                foreach ($headers as $header) {
                    list($key, $value) = explode(': ', $header, 2);
                    $newHeaders[$key] = $value;
                }
                $headers = $newHeaders; // Convert to new format
            }
        }
    }

    /**
     * Truncate a long string message.
     *
     * @param string $message
     * @param integer $len
     * @return string
     */
    public static function truncate($message, $len = 100)
    {
        if ($message && strlen($message) > $len) {
            $message = sprintf('%s... %d more', substr($message, 0, $len), strlen($message) - $len);
        }

        return $message;
    }

    /**
     * Convert array or any value for string representaion.
     *
     * @param array|mixed $value
     * @return string
     */
    public static function toStr($value)
    {
        $result = null;
        if (is_array($value)) {
            $values = [];
            $hasKeys = true;
            foreach ($value as $k => $v) {
                $values[$k] = static::toStr($v);
                if (is_int($k) && $hasKeys) {
                    $hasKeys = false;
                }
            }
            if ($hasKeys) {
                $values = array_map(
                    function($key, $value) {
                        return "\"$key\":$value";
                    },
                    array_keys($values),
                    $values
                );
                $result = sprintf('{%s}', implode(',', $values));
            } else {
                $result = sprintf('[%s]', implode(',', $values));
            }
        } else if (is_resource($value)) {
            fseek($value, 0);
            $result = '<' . static::truncate(stream_get_contents($value), 32) . '>';
        } else if (is_string($value)) {
            $result = '"' . $value . '"';
        } else {
            $result = var_export($value, true);
        }

        return $result;
    }

    /**
     * Create a resource from value.
     *
     * @param string $value
     * @return resource
     */
    public static function toResource($value)
    {
        if (false !== ($result = fopen('php://memory', 'w+'))) {
            fwrite($result, $value);
        }

        return $result;
    }
}