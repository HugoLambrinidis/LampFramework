<?php

namespace Controller;

use src\connection;

abstract class AbstractController extends connection {

    public function render($data = null, $view) {

        $file = "./Site/View/".$view.".php";

        return new ViewController($data, $file);
    }

}