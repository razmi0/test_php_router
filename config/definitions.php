<?php

use App\Lib\HTTP\Request;
use App\Lib\HTTP\Response;

return [
    Request::class => Request::getInstance(),
    Response::class => Response::getInstance()
];
