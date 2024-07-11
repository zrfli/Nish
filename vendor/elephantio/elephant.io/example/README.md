# Elephant.io Example

This examples bellow shows typical Elephant.io usage to connect to socket.io server.

## Available examples

| Description                                 | Server                                                      | Client                                            |
|---------------------------------------------|-------------------------------------------------------------|---------------------------------------------------|
| Acknowledgement                             | [serve-ack.js](./server/serve-ack.js)                       | [ack.php](./client/ack.php)                       |
| Sending and receiving binary data           | [serve-binary-event.js](./server/serve-binary-event.js)     | [binary-event.php](./client/binary-event.php)     |
| Error handling                              | [serve-error-handling.js](./server/serve-error-handling.js) | [error-handling.php](./client/error-handling.php) |
| Authentication using handshake              | [serve-handshake-auth.js](./server/serve-handshake-auth.js) | [handshake-auth.php](./client/handshake-auth.php) |
| Authentication using `Authorization` header | [serve-header-auth.js](./server/serve-header-auth.js)       | [header-auth.php](./client/header-auth.php)       |
| Keep alive                                  | [serve-keep-alive.js](./server/serve-keep-alive.js)         | [keep-alive.php](./client/keep-alive.php)         |
| Polling                                     | [serve-polling.js](./server/serve-polling.js)               | [polling.php](./client/polling.php)               |

## Run server part first

Ensure Nodejs already installed on your system, then issue:

```sh
cd server
npm install
npm start
```

## Run actual example

On another terminal, issue:

```sh
cd client
php binary-event.php
```

A log file named `socket.log` will be created upon running the example which
contains the log when connecting to socket server.

## Run example to target specific socket.io version

Install specific version of `socket.io` package:

```sh
cd server
npm install socket.io@0
```

Change the client version:

```sh
cd client
vi common.php
```

```diff
diff --git a/example/client/common.php b/example/client/common.php
index 0bc2023..95af2c4 100644
--- a/example/client/common.php
+++ b/example/client/common.php
@@ -26,7 +26,7 @@ require __DIR__ . '/../../vendor/autoload.php';
  */
 function client_version($version = null)
 {
-    static $client = Client::CLIENT_4X; // default client version
+    static $client = Client::CLIENT_0X; // default client version
     if (null !== $version) {
         $client = $version;
     }
```

Run the example as shown above.
