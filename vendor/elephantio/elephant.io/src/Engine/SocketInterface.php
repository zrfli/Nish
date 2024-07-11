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

/**
 * Socket host interface.
 *
 * @author Toha <tohenk@yahoo.com>
 */
interface SocketInterface
{
    /**
     * Get options.
     *
     * @return \ElephantIO\Engine\Option
     */
    public function getOptions();

    /**
     * Get socket stream.
     *
     * @return \ElephantIO\Stream\StreamInterface
     */
    public function getStream();

    /**
     * Get stream context.
     *
     * @return array
     */
    public function getContext();

    /**
     * Get cookies.
     *
     * @return array
     */
    public function getCookies();

    /**
     * Get session.
     *
     * @return \ElephantIO\Engine\Session
     */
    public function getSession();

    /**
     * Build query string parameters.
     *
     * @param string $transport
     * @return array
     */
    public function buildQueryParameters($transport);

    /**
     * Build query from parameters.
     *
     * @param array $query
     * @return string
     */
    public function buildQuery($query);
}
