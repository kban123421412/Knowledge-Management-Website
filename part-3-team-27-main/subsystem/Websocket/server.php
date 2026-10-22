<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Chat;

require dirname(__DIR__, 2) . '/subsystem/Websocket/vendor/autoload.php';
require __DIR__ . '/Chat.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    9000
);

echo "WebSocket server running on port 9000...\n";
$server->run();
