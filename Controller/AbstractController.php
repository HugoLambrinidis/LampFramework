<?php

namespace Controller;

abstract class AbstractController {

    public function render($data = null, $view) {

        $file = "./Site/View/".$view.".php";

        return new ViewController($data, $file);
    }

}