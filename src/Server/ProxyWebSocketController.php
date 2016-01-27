<?php

namespace MadMind\RROP\Server;

use MadMind\RROP\Common\WsRequest;
use MadMind\RROP\Server\Exception\NoClientException;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ProxyWebSocketController implements MessageComponentInterface
{
    /**
     * @var ConnectionStorage
     */
    protected $clients;
    /**
     * @var RequestStorage
     */
    protected $requests;

    public function __construct()
    {
        $this->clients = new ConnectionStorage();
        $this->requests = new RequestStorage();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        /* @var $request \Guzzle\Http\Message\EntityEnclosingRequest */
        $request = $conn->WebSocket->request;
        $key = $request->getQuery()['key'];

        // TODO: Auth client by key.
        $this->clients->attach($conn, $key);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $msg = substr($msg, 9);
        $del = strpos($msg, '|');
        $id = substr($msg, 0, $del);
        $msg = substr($msg, $del + 1);
        $json = json_decode($msg, true);

        printf("Response[%s]: %s\r\n", $id, $json['statusCode']);

        /* @var $response \React\Http\Response */
        $response = $this->requests->getResponse($id);
        $response->writeHead($json['statusCode'], $json['headers']);
        $response->end($json['body']);
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    public function sendRequest($clientId, WsRequest $request, \React\Http\Response $response)
    {
        $conn = $this->clients->getClientById($clientId);
        if (!$conn) {
            throw new NoClientException();
        }

        printf(
            "Request[%s]: %s %s\r\n",
            $request->getId(),
            $request->getRequest()->getMethod(),
            $request->getRequest()->getPath()
        );

        $serialized = $request->serialize();

        $this->requests->add($request->getId(), $request, $response);

        $conn->send('request|'.$serialized);
    }
}
