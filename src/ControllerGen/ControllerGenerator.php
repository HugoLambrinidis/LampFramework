<?php

include_once "ControllerConf.php";

$namespace = $argv[1];
$name = $argv[2];
$viewName = $argv[3];
$conf = $argv[4];
$variables = [];
$j = 0;
if (sizeof($argv) > 4) {
    for ($i = 5; $i < sizeof($argv); $i++) {
        $variables[$j] = $argv[$i];
        $j++;
    }
} else {
    $conf = false;
}

new \src\ControllerGen\ControllerConf($name, $namespace, $variables, $conf, $viewName);