# Elephant.io

![Build Status](https://github.com/ElephantIO/elephant.io/actions/workflows/continuous-integration.yml/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/elephantio/elephant.io/v/stable.svg)](https://packagist.org/packages/elephantio/elephant.io)
[![Total Downloads](https://poser.pugx.org/elephantio/elephant.io/downloads.svg)](https://packagist.org/packages/elephantio/elephant.io) 
[![License](https://poser.pugx.org/elephantio/elephant.io/license.svg)](https://packagist.org/packages/elephantio/elephant.io)

```
        ___     _,.--.,_         Elephant.io is a socket.io client written in PHP.
      .-~   ~--"~-.   ._ "-.     Its goal is to ease the communications between your
     /      ./_    Y    "-. \    PHP application and a socket.io server.
    Y       :~     !         Y
    lq p    |     /         .|   Requires PHP 7.2 and openssl, licensed under
 _   \. .-, l    /          |j   the MIT License
()\___) |/   \_/";          !
 \._____.-~\  .  ~\.      ./
            Y_ Y_. "vr"~  T      Built-in engines:
            (  (    |L    j      - Socket.io 4.x, 3.x, 2.x, 1.x
            [nn[nn..][nn..]      - Socket.io 0.x (courtesy of @kbu1564)
          ~~~~~~~~~~~~~~~~~~~
```

## Installation

We are suggesting you to use composer, using `composer require elephantio/elephant.io`. For other ways, you can check the release page, or the git clone urls.

## Usage

To use Elephant.io to communicate with socket.io server is described as follows.

```php
<?php

use ElephantIO\Client;

$url = 'http://localhost:8080';

// if client option is omitted then it will use latest client available,
// aka. version 4.x
$options = ['client' => Client::CLIENT_4X];

$client = Client::create($url, $options);
$client->connect();
$client->of('/'); // can be omitted if connecting to default namespace

// emit an event to the server
$data = ['username' => 'my-user'];
$client->emit('get-user-info', $data);

// wait an event to arrive
// beware when waiting for response from server, the script may be killed if
// PHP max_execution_time is reached
if ($packet = $client->wait('user-info')) {
    // an event has been received, the result will be a \ElephantIO\Engine\Packet class
    // data property contains the first argument
    // args property contains array of arguments, [$data, ...]
    $data = $packet->data;
    $args = $packet->args;
    // access data
    $email = $data['email'];
}

// end session
$client->disconnect();
```

## Options

Elephant.io accepts options to configure the internal engine such as passing headers, providing additional
authentication token, or providing stream context.

* `headers`

  An array of key-value pair to be sent as request headers. For example, pass a bearer token to the server.

  ```php
  <?php

  $options = [
      'headers' => [
          'Authorization' => 'Bearer MYTOKEN',
      ]
  ];
  $client = Client::create($url, $options);
  ```

* `auth`

  Specify an array to be passed as handshake. The data to be passed depends on the server implementation.

  ```php
  <?php

  $options = [
      'auth' => [
          'user' => 'user@example.com',
          'token' => 'my-secret-token',
      ]
  ];
  $client = Client::create($url, $options);
  ```

  On the server side, those data can be accessed using:

  ```js
  io.use((socket, next) => {
      const user = socket.handshake.auth.user;
      const token = socket.handshake.auth.token;
      // do something with data
  });
  ```

* `context`

  A [stream context](https://www.php.net/manual/en/function.stream-context-create.php) options for the socket stream.

  ```php
  <?php

  $options = [
      'context' => [
          'http' => [],
          'ssl' => [],
      ]
  ];
  $client = Client::create($url, $options);
  ```

* `persistent`

  The socket connection by default will be using a persistent connection. If you prefer for some
  reasons to disable it, set `persistent` to `false`.

* `reuse_connection`

  Enable or disable existing connection reuse, by default the engine will reuse existing
  connection. To disable to reuse existing connection set `reuse_connection` to `false`.

* `transports`

  An array of enabled transport. Set to `null` or combination of `polling` or `websocket` to enable
  specific transport.

* `transport`

  Initial socket transport used to connect to server, either `polling` or `websocket` is supported.
  The default transport used is `polling` and it will be upgraded to `websocket` if the server offer
  to upgrade and `transports` option does not exclude `websocket`.

  To connect to server with `polling` only transport:

  ```php
  <?php

  $options = [
      'transport' => 'polling',     // can be omitted as polling is default transport
      'transports' => ['polling'],
  ];
  $client = Client::create($url, $options);
  ```

  To connect to server with `websocket` only transport:

  ```php
  <?php

  $options = [
      'transport' => 'websocket',
  ];
  $client = Client::create($url, $options);
  ```

* `binary_as_resource`

  When client receives a binary data, by default it will be presented as `resource`.
  Set to `false` to retain it as string instead. Be careful, when you read the resource
  content, it is necessary to seek the stream to the begining using `fseek($handle, 0)`
  first. 

## Methods

Elephant.io client (`ElephantIO\Client`) provides the following api methods:

* ~~`initialize()`~~

   An alias to `connect()`.

* `connect()`

  Connect to socket.io server. In case of server connection is unsuccessful, an exception
  `ElephantIO\Exception\SocketException` will be thrown. It also connects to default
  `/` namespace and will trigger `ElephantIO\Exception\UnsuccessfulOperationException`
  upon unsuccessful attempts.

* ~~`close()`~~

   An alias to `disconnect()`.

* `disconnect()`

  Disconnect from server and free some resources.

* `of($namespace)`

  Connect to a namespace, see `connect()` above for possible errors.

* `emit($event, array $args, $ack = null)`

  Send an event to server. To request an acknowledgement from server, set `$ack` to `true`.
  When an acknowledgement is requested, a packet will be returned on successful operation.

* `wait($event, $timeout = 0)`

  Wait an event to be received from server.

* `drain($timeout = 0)`

  Drain and get returned packet from server, used to receive data from server
  when we are not expecting an event to arrive.

* `ack($packet, array $args)`

  Acknowledge a received event.

* `getEngine()`

  Get the underlying socket engine.

## Debugging

It's sometime necessary to get the verbose output for debugging. Elephant.io utilizes `Psr\Log\LoggerInterface`
for this purpose.

```php
<?php

use ElephantIO\Client;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LogLevel;

$url = 'http://localhost:8080';
$logfile = __DIR__ . '/socket.log';

$logger = new Logger('elephant.io');
$logger->pushHandler(new StreamHandler($logfile, LogLevel::DEBUG)); // set LogLevel::INFO for brief logging

$options = ['logger' => $logger];

$client = Client::create($url, $options);
```

Here is an example of debug logging:

```log
[2024-02-07T19:15:36.334185+07:00] elephant.io.INFO: Connecting to server [] []
[2024-02-07T19:15:36.339640+07:00] elephant.io.INFO: Starting handshake [] []
[2024-02-07T19:15:36.342198+07:00] elephant.io.INFO: Stream connect: tcp://localhost:14000 [] []
[2024-02-07T19:15:36.357717+07:00] elephant.io.DEBUG: Stream write: GET /socket.io/?EIO=4&transport=polling&t=Os3VhP0 HTTP/1.1 [] []
[2024-02-07T19:15:36.357935+07:00] elephant.io.DEBUG: Stream write: Host: localhost:14000 [] []
[2024-02-07T19:15:36.358062+07:00] elephant.io.DEBUG: Stream write: Connection: keep-alive [] []
[2024-02-07T19:15:36.358157+07:00] elephant.io.DEBUG: Stream write:  [] []
[2024-02-07T19:15:36.358250+07:00] elephant.io.DEBUG: Stream write:  [] []
[2024-02-07T19:15:36.370573+07:00] elephant.io.DEBUG: Stream receive: HTTP/1.1 200 OK  [] []
[2024-02-07T19:15:36.386194+07:00] elephant.io.DEBUG: Stream receive: Content-Type: text/plain; charset=UTF-8  [] []
[2024-02-07T19:15:36.401799+07:00] elephant.io.DEBUG: Stream receive: Content-Length: 118  [] []
[2024-02-07T19:15:36.417080+07:00] elephant.io.DEBUG: Stream receive: cache-control: no-store  [] []
[2024-02-07T19:15:36.433208+07:00] elephant.io.DEBUG: Stream receive: Date: Wed, 07 Feb 2024 12:15:36 GMT  [] []
[2024-02-07T19:15:36.448896+07:00] elephant.io.DEBUG: Stream receive: Connection: keep-alive  [] []
[2024-02-07T19:15:36.464189+07:00] elephant.io.DEBUG: Stream receive: Keep-Alive: timeout=5  [] []
[2024-02-07T19:15:36.479713+07:00] elephant.io.DEBUG: Stream receive:   [] []
[2024-02-07T19:15:36.495378+07:00] elephant.io.DEBUG: Stream receive: 0{"sid":"JnwzDxAn3tKdHwa2AAAD","upgrades":["websocket"],"pingInterval":25000,"pingTimeout":20000,"ma... 18 more [] []
[2024-02-07T19:15:36.497365+07:00] elephant.io.INFO: Got packet: OPEN{data:{"sid":"JnwzDxAn3tKdHwa2AAAD","upgrades":["websocket"],"pingInterval":25000,"pingTimeout":... 28 more [] []
[2024-02-07T19:15:36.498271+07:00] elephant.io.INFO: Handshake finished with SESSION{id:'JnwzDxAn3tKdHwa2AAAD',upgrades:["websocket"],timeouts:{"interval":25,"timeout":20},max_payload:1000000} [] []
[2024-02-07T19:15:36.498378+07:00] elephant.io.INFO: Starting namespace connect [] []
[2024-02-07T19:15:36.498473+07:00] elephant.io.DEBUG: Send data: 40 [] []
[2024-02-07T19:15:36.498862+07:00] elephant.io.DEBUG: Stream write: POST /socket.io/?EIO=4&transport=polling&t=Os3VhP0.0&sid=JnwzDxAn3tKdHwa2AAAD HTTP/1.1 [] []
[2024-02-07T19:15:36.498981+07:00] elephant.io.DEBUG: Stream write: Host: localhost:14000 [] []
[2024-02-07T19:15:36.499058+07:00] elephant.io.DEBUG: Stream write: Content-Type: text/plain; charset=UTF-8 [] []
[2024-02-07T19:15:36.499136+07:00] elephant.io.DEBUG: Stream write: Content-Length: 2 [] []
[2024-02-07T19:15:36.499218+07:00] elephant.io.DEBUG: Stream write: Connection: keep-alive [] []
[2024-02-07T19:15:36.499297+07:00] elephant.io.DEBUG: Stream write:  [] []
[2024-02-07T19:15:36.499376+07:00] elephant.io.DEBUG: Stream write: 40 [] []
[2024-02-07T19:15:36.511209+07:00] elephant.io.DEBUG: Stream receive: HTTP/1.1 200 OK  [] []
[2024-02-07T19:15:36.526684+07:00] elephant.io.DEBUG: Stream receive: Content-Type: text/html  [] []
[2024-02-07T19:15:36.543098+07:00] elephant.io.DEBUG: Stream receive: Content-Length: 2  [] []
[2024-02-07T19:15:36.559652+07:00] elephant.io.DEBUG: Stream receive: cache-control: no-store  [] []
[2024-02-07T19:15:36.575655+07:00] elephant.io.DEBUG: Stream receive: Date: Wed, 07 Feb 2024 12:15:36 GMT  [] []
[2024-02-07T19:15:36.591805+07:00] elephant.io.DEBUG: Stream receive: Connection: keep-alive  [] []
[2024-02-07T19:15:36.608440+07:00] elephant.io.DEBUG: Stream receive: Keep-Alive: timeout=5  [] []
[2024-02-07T19:15:36.624682+07:00] elephant.io.DEBUG: Stream receive:   [] []
[2024-02-07T19:15:36.639941+07:00] elephant.io.DEBUG: Stream receive: ok [] []
[2024-02-07T19:15:36.640613+07:00] elephant.io.DEBUG: Stream write: GET /socket.io/?EIO=4&transport=polling&t=Os3VhP0.1&sid=JnwzDxAn3tKdHwa2AAAD HTTP/1.1 [] []
[2024-02-07T19:15:36.640741+07:00] elephant.io.DEBUG: Stream write: Host: localhost:14000 [] []
[2024-02-07T19:15:36.640822+07:00] elephant.io.DEBUG: Stream write: Connection: keep-alive [] []
[2024-02-07T19:15:36.641043+07:00] elephant.io.DEBUG: Stream write:  [] []
[2024-02-07T19:15:36.641146+07:00] elephant.io.DEBUG: Stream write:  [] []
[2024-02-07T19:15:36.655816+07:00] elephant.io.DEBUG: Stream receive: HTTP/1.1 200 OK  [] []
[2024-02-07T19:15:36.671726+07:00] elephant.io.DEBUG: Stream receive: Content-Type: text/plain; charset=UTF-8  [] []
[2024-02-07T19:15:36.686998+07:00] elephant.io.DEBUG: Stream receive: Content-Length: 32  [] []
[2024-02-07T19:15:36.702384+07:00] elephant.io.DEBUG: Stream receive: cache-control: no-store  [] []
[2024-02-07T19:15:36.718665+07:00] elephant.io.DEBUG: Stream receive: Date: Wed, 07 Feb 2024 12:15:36 GMT  [] []
[2024-02-07T19:15:36.733934+07:00] elephant.io.DEBUG: Stream receive: Connection: keep-alive  [] []
[2024-02-07T19:15:36.749578+07:00] elephant.io.DEBUG: Stream receive: Keep-Alive: timeout=5  [] []
[2024-02-07T19:15:36.765304+07:00] elephant.io.DEBUG: Stream receive:   [] []
[2024-02-07T19:15:36.780796+07:00] elephant.io.DEBUG: Stream receive: 40{"sid":"JcKcO8ySN_Pt7W3nAAAE"} [] []
[2024-02-07T19:15:36.781013+07:00] elephant.io.DEBUG: Got data: 40{"sid":"JcKcO8ySN_Pt7W3nAAAE"} [] []
[2024-02-07T19:15:36.781207+07:00] elephant.io.INFO: Got packet: MESSAGE{type:'connect',nsp:'',data:{"sid":"JcKcO8ySN_Pt7W3nAAAE"}} [] []
[2024-02-07T19:15:36.781283+07:00] elephant.io.INFO: Namespace connect completed [] []
[2024-02-07T19:15:36.781388+07:00] elephant.io.INFO: Starting websocket upgrade [] []
[2024-02-07T19:15:36.782101+07:00] elephant.io.DEBUG: Stream write: GET /socket.io/?EIO=4&transport=websocket&t=Os3VhP0.2&sid=JnwzDxAn3tKdHwa2AAAD HTTP/1.1 [] []
[2024-02-07T19:15:36.782229+07:00] elephant.io.DEBUG: Stream write: Host: localhost:14000 [] []
[2024-02-07T19:15:36.782308+07:00] elephant.io.DEBUG: Stream write: Upgrade: websocket [] []
[2024-02-07T19:15:36.782379+07:00] elephant.io.DEBUG: Stream write: Connection: Upgrade [] []
[2024-02-07T19:15:36.782456+07:00] elephant.io.DEBUG: Stream write: Sec-WebSocket-Key: uActUggIt5OnwQWOaqCTJw== [] []
[2024-02-07T19:15:36.782533+07:00] elephant.io.DEBUG: Stream write: Sec-WebSocket-Version: 13 [] []
[2024-02-07T19:15:36.782609+07:00] elephant.io.DEBUG: Stream write: Origin: * [] []
[2024-02-07T19:15:36.782683+07:00] elephant.io.DEBUG: Stream write:  [] []
[2024-02-07T19:15:36.782760+07:00] elephant.io.DEBUG: Stream write:  [] []
[2024-02-07T19:15:36.796297+07:00] elephant.io.DEBUG: Stream receive: HTTP/1.1 101 Switching Protocols  [] []
[2024-02-07T19:15:36.811668+07:00] elephant.io.DEBUG: Stream receive: Upgrade: websocket  [] []
[2024-02-07T19:15:36.813973+07:00] elephant.io.DEBUG: Stream receive: Connection: Upgrade  [] []
[2024-02-07T19:15:36.814801+07:00] elephant.io.DEBUG: Stream receive: Sec-WebSocket-Accept: WV0ttuG/wPXtIQ8Z3LMW/OilwsM=  [] []
[2024-02-07T19:15:36.831356+07:00] elephant.io.DEBUG: Stream receive:   [] []
[2024-02-07T19:15:36.831651+07:00] elephant.io.DEBUG: Send data: 5 [] []
[2024-02-07T19:15:36.836271+07:00] elephant.io.DEBUG: Stream write: ��[��# [] []
[2024-02-07T19:15:36.847342+07:00] elephant.io.INFO: Websocket upgrade completed [] []
[2024-02-07T19:15:36.847600+07:00] elephant.io.INFO: Connected to server [] []
[2024-02-07T19:15:36.847734+07:00] elephant.io.INFO: Setting namespace {"namespace":"/keep-alive"} []
[2024-02-07T19:15:36.847861+07:00] elephant.io.DEBUG: Send data: 40/keep-alive [] []
[2024-02-07T19:15:36.848210+07:00] elephant.io.DEBUG: Stream write: ���U�x�e��0�U�9�� [] []
[2024-02-07T19:15:36.865233+07:00] elephant.io.DEBUG: Stream receive: �, [] []
[2024-02-07T19:15:36.865549+07:00] elephant.io.DEBUG: Stream receive: 40/keep-alive,{"sid":"CQ39APtaje18wJVrAAAF"} [] []
[2024-02-07T19:15:36.866633+07:00] elephant.io.DEBUG: Got data: 40/keep-alive,{"sid":"CQ39APtaje18wJVrAAAF"} [] []
[2024-02-07T19:15:36.866831+07:00] elephant.io.INFO: Got packet: MESSAGE{type:'connect',nsp:'/keep-alive',data:{"sid":"CQ39APtaje18wJVrAAAF"}} [] []
[2024-02-07T19:15:36.866936+07:00] elephant.io.INFO: Emitting a new event {"event":"message","args":{"message":"A message"}} []
[2024-02-07T19:15:36.867203+07:00] elephant.io.DEBUG: Send data: 42/keep-alive,["message",{"message":"A message"}] [] []
[2024-02-07T19:15:36.867640+07:00] elephant.io.DEBUG: Stream write: ��װ�wセ���Z������U������U�˶����նM������ն � [] []
[2024-02-07T19:15:36.873410+07:00] elephant.io.INFO: Waiting for event {"event":"message"} []
[2024-02-07T19:15:36.873747+07:00] elephant.io.DEBUG: Stream receive: �* [] []
[2024-02-07T19:15:36.873881+07:00] elephant.io.DEBUG: Stream receive: 42/keep-alive,["message",{"success":true}] [] []
[2024-02-07T19:15:36.874041+07:00] elephant.io.DEBUG: Got data: 42/keep-alive,["message",{"success":true}] [] []
[2024-02-07T19:15:36.874223+07:00] elephant.io.INFO: Got packet: MESSAGE{type:'event',nsp:'/keep-alive',event:'message',args:[{"success":true}]} [] []
[2024-02-07T19:16:01.373824+07:00] elephant.io.DEBUG: Stream receive: � [] []
[2024-02-07T19:16:01.374082+07:00] elephant.io.DEBUG: Stream receive: 2 [] []
[2024-02-07T19:16:01.374187+07:00] elephant.io.DEBUG: Got data: 2 [] []
[2024-02-07T19:16:01.374327+07:00] elephant.io.INFO: Got packet: PING{} [] []
[2024-02-07T19:16:01.374396+07:00] elephant.io.DEBUG: Got PING, sending PONG [] []
[2024-02-07T19:16:01.374459+07:00] elephant.io.DEBUG: Send data: 3 [] []
[2024-02-07T19:16:01.374879+07:00] elephant.io.DEBUG: Stream write: ���z��� [] []
[2024-02-07T19:16:07.392523+07:00] elephant.io.INFO: Emitting a new event {"event":"message","args":{"message":"Last message"}} []
[2024-02-07T19:16:07.392756+07:00] elephant.io.DEBUG: Send data: 42/keep-alive,["message",{"message":"Last message"}] [] []
[2024-02-07T19:16:07.393127+07:00] elephant.io.DEBUG: Stream write: ��j^4=|cb:j{a*I5cadaw5F}0zuav c0-HJsd&rusp$oJ [] []
[2024-02-07T19:16:07.397817+07:00] elephant.io.INFO: Waiting for event {"event":"message"} []
[2024-02-07T19:16:07.398116+07:00] elephant.io.DEBUG: Stream receive: �* [] []
[2024-02-07T19:16:07.398227+07:00] elephant.io.DEBUG: Stream receive: 42/keep-alive,["message",{"success":true}] [] []
[2024-02-07T19:16:07.398348+07:00] elephant.io.DEBUG: Got data: 42/keep-alive,["message",{"success":true}] [] []
[2024-02-07T19:16:07.398520+07:00] elephant.io.INFO: Got packet: MESSAGE{type:'event',nsp:'/keep-alive',event:'message',args:[{"success":true}]} [] []
[2024-02-07T19:16:07.399750+07:00] elephant.io.INFO: Closing connection to server [] []
[2024-02-07T19:16:07.399972+07:00] elephant.io.DEBUG: Send data: 1 [] []
[2024-02-07T19:16:07.400294+07:00] elephant.io.DEBUG: Stream write: ����� [] []
```

## Examples

The [the example directory](/example) shows how to get a basic knowledge of library usage.

## Special Thanks

Special thanks goes to Mark Karpeles who helped the project founder to understand the way websockets works.
