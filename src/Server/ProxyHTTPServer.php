<?php

namespace MadMind\RROP\Server;

use MadMind\RROP\Common\WsRequest;
use React\EventLoop\LoopInterface;
use React\Http\Request;
use React\Http\Response;
use React\Http\Server as ReactHttpServer;
use React\Socket\Server as ReactSocketServer;

class ProxyHTTPServer implements ProxyServerInterface
{
    /**
     * @var ReactSocketServer
     */
    protected $socket;

    /**
     * @var ReactHttpServer
     */
    protected $http;

    /**
     * @var ProxyWebSocketController
     */
    protected $controller;

    protected $clientId;

    public function __construct(
        LoopInterface $loop,
        ProxyWebSocketController $controller,
        $clientId
    ) {
        $this->clientId = $clientId;
        $this->controller = $controller;

        $this->socket = $this->createSocket($loop);
        $this->http = $this->createHttpServer($this->socket);

        $this->http->on('request', [$this, 'handleHttpRequest']);
    }

    protected function createSocket(LoopInterface $loop)
    {
        return new ReactSocketServer($loop);
    }

    protected function createHttpServer(ReactSocketServer $socket)
    {
        return new ReactHttpServer($socket);
    }

    public function run()
    {
        // TODO: Read config value.
        $this->socket->listen(1337);
    }

    public function handleHttpRequest(Request $request, Response $response)
    {
        // Send request to WS.
        $proxyRequest = new WsRequest($request);

        try {
            $this->controller->sendRequest($this->clientId, $proxyRequest, $response);
        } catch (\Exception $e) {
            $response->writeHead(502);
            $response->end('Bad Gateway');
        }
    }
}
