<?php

namespace MadMind\RROP\Common;

use React\Http\Request;

class WsRequest
{
    protected $request;
    protected $id;

    public function __construct(Request $request = null)
    {
        $this->request = $request;
        $this->id = uniqid();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function serialize()
    {
        $headers = $this->request->getHeaders();

        $data = [
            'id' => $this->id,
            'method' => $this->request->getMethod(),
            'path' => $this->request->getPath(),
            'query' => $this->request->getQuery(),
            'httpVersion' => $this->request->getHttpVersion(),
            'headers' => $headers,
        ];

        return json_encode($data);
    }
}
