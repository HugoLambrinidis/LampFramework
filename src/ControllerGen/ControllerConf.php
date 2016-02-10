<?php

namespace src\ControllerGen;

class ControllerConf {

    private $file_content;

    public function __construct($controllerName, $namespace,$variables, $conf, $viewName) {

        if (!is_dir("../../Site/Controller/".$namespace)) {
            mkdir("../../Site/Controller/".$namespace);
            mkdir("../../Site/View/".$namespace);
        }

        $this->file_content = "<?php\n\n namespace Site\\Controller\\".$namespace.";\n\n use Controller\\AbstractController; \n\n class ".$controllerName." extends AbstractController {\n\n";

        if (is_array($variables) ) {
            foreach ($variables as $variable) {
                $this->file_content .= "public \$" . $variable . ";\n\n";
            }
        }

        if ($conf) {
            foreach($variables as $variable) {
                $this->file_content .= "public function set".ucfirst($variable)."(\$var){\n\n \$this->".$variable." = \$var; \n}\n\n";
                $this->file_content .= "public function get".ucfirst($variable)."(){\n\n return \$this->".$variable.";\n}\n\n";
            }
        }
        $this->file_content .= "public function __construct(){\n //set your controller behavior here \n }\n\n";
        $this->file_content .= "}";
        file_put_contents("../../Site/Controller/".$namespace."/".$controllerName.".php", $this->file_content);
        $view = fopen("../../Site/View/".$namespace."/".$viewName.".php", "w+");
        fclose($view);

    }
}