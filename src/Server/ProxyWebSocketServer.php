<?php

namespace MadMind\RROP\Server;

use React\EventLoop\LoopInterface;

class ProxyWebSocketServer implements ProxyServerInterface
{
    protected $socket;

    public function __construct(LoopInterface $loop, ProxyWebSocketController $wsController)
    {
        $ws = new \Ratchet\WebSocket\WsServer($wsController);
        $ws->disableVersion(0);

        $this->socket = new \React\Socket\Server($loop);

        $wsServer = new \Ratchet\Server\IoServer(new \Ratchet\Http\HttpServer($ws), $this->socket);

    }

    public function run()
    {
        $this->socket->listen(1338);
    }
}
