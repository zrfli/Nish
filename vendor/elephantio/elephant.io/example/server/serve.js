/**
 * This file is part of the Elephant.io package
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 *
 * @copyright Wisembly
 * @license   http://www.opensource.org/licenses/MIT-License MIT License
 */

const { Server, Namespace } = require('socket.io');

/**
 * A base class for example server.
 */
class ExampleServer {

    /** @type {Server} */
    io = null

    /** @type {Namespace} */
    nsp = null

    /** @type {string} */
    namespace = null

    /**
     * Constructor.
     *
     * @param {Server} io
     */
    constructor(io) {
        this.io = io;
        this.initialize();
        this.nsp = io.of(this.namespace ? this.namespace : '/');
    }

    /**
     * Initialize server.
     */
    initialize() {
    }

    /**
     * Do handle example such as listen for events or applying middleware.
     * 
     * @example
     * this.nsp.on('connection', socket => {
     *     this.log('connected: %s', socket.id);
     *     socket.on('disconnect', () => {
     *         this.log('disconnected: %s', socket.id);
     *     });
     * });
     */
    handle() {
    }

    /**
     * Log to console.
     *
     * @param  {...any} args
     */
    log(...args) {
        if (args.length) {
            const ns = this.namespace ? this.namespace : '/';
            if (typeof args[0] === 'string') {
                args[0] = `${ns}: ${args[0]}`;
            } else {
                args.unshift(`${ns}: `);
            }
        }
        console.log.apply(this, args);
    }
}

module.exports = ExampleServer;