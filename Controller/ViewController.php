<?php

namespace Controller;

class ViewController {

    private $data;

    public function __construct($data, $file) {
        $this->data = $data;
        include_once $file;
    }

}