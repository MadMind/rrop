<?php
$key = 'rrop-test';
$server = 'rrop.mad.uk.to';
//$server = '127.0.0.1:1338';

require __DIR__.'/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$connector = new Ratchet\Client\Factory($loop);

$connector('ws://'.$server.'/?key='.$key)
    ->then(
        function (Ratchet\Client\WebSocket $conn) {
            $conn->on('message', 'processRequest');
        },
        function ($e) use ($loop) {
            echo "Could not connect: {$e->getMessage()}\n";
            $loop->stop();
        }
    );

$loop->run();

function processRequest($msg, $conn)
{
    $json = json_decode(substr($msg, 8), true);
    printf("Request: %s %s\r\n", $json['method'], $json['path']);

    $headers = $json['headers'];
    $id = $json['id'];
    unset($headers['Host']);

    $json['headers']['Host'] = 'localhost';

    $client = new GuzzleHttp\Client(['base_uri' => 'http://'.$json['headers']['Host'], 'http_errors' => false]);
    $response = $client->request($json['method'], $json['path'], ['headers' => $headers]);


    $json = [
        'headers' => $response->getHeaders(),
        'statusCode' => $response->getStatusCode(),
        'body' => $response->getBody()->getContents(),
        'version' => $response->getProtocolVersion(),
    ];

    printf("Response: %s\r\n", $json['statusCode']);

    $conn->send('response|'.$id.'|'.json_encode($json));
}
