<?php

namespace MadMind\RROP\Server;

use React\EventLoop\Factory as ReactLoopFactory;

class Server
{
    /**
     * @var \React\EventLoop\LoopInterface
     */
    protected $loop;

    /**
     * @var ProxyHTTPServer
     */
    protected $httpServer;

    /**
     * @var ProxyWebSocketServer
     */
    protected $webSocketServer;

    public function __construct($clientId)
    {
        $this->loop = $this->createLoop();
        $controller = $this->createController();

        $this->httpServer = $this->createHttpServer($controller, $clientId);
        $this->webSocketServer = $this->createWebSocketServer($controller);
    }

    protected function createLoop()
    {
        return ReactLoopFactory::create();
    }

    protected function createController()
    {
        return new ProxyWebSocketController();
    }

    protected function createHttpServer(ProxyWebSocketController $controller, $clientId)
    {
        return new ProxyHTTPServer($this->loop, $controller, $clientId);
    }

    protected function createWebSocketServer(ProxyWebSocketController $controller)
    {
        return new ProxyWebSocketServer($this->loop, $controller);
    }

    public function run()
    {
        $this->httpServer->run();
        $this->webSocketServer->run();
        $this->loop->run();
    }

}
