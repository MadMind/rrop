<?php

namespace MadMind\RROP\Server;

class ConnectionStorage extends \SplObjectStorage
{
    /**
     * @param $id
     * @return \Ratchet\ConnectionInterface;
     */
    public function getClientById($id)
    {
        $this->rewind();
        while ($this->valid()) {
            $object = $this->current();
            if ($id === $this->getInfo()) {
                $this->rewind();

                return $object;
            }
            $this->next();
        }

        return null;
    }
}
