<?php

namespace App\Lib\HTTP;

class AbsRequest
{

    public function __clone()
    {
        throw new \Exception("Cannot clone a singleton");
    }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton");
    }

    public function __sleep()
    {
        throw new \Exception("Cannot serialize a singleton");
    }
}
