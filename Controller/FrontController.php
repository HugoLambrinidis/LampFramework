<?php

namespace Controller;

use Routing\Routing;

Class FrontController {

    public function __construct()
    {
        $conf = json_decode(file_get_contents("conf.json"));
        $current_request = $_SERVER['REQUEST_URI'];
        $request = explode($conf->base_url, $current_request);
        $controller = new Routing($request[1]);


    }
}