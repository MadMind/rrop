<?php

namespace MadMind\RROP\Server;

class RequestStorage
{
    protected $requests = [];

    public function add($id, $request, $response)
    {
        $this->requests[$id] = ['request' => $request, 'response' => $response];
    }

    public function getResponse($id) {
        return $this->requests[$id]['response'];
    }
}
