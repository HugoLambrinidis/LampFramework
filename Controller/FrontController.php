<?php

namespace Controller;

Class FrontController {

    public function __construct()
    {
        $conf = json_decode(file_get_contents("conf.json"));
        $current_request = $_SERVER['REQUEST_URI'];
        $request = explode($conf->url, $current_request);

        // instantiate router with $request[1] param
    }
}