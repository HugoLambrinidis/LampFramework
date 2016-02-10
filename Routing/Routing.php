<?php

namespace Routing;

 class Routing {

    private $url;

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function __construct($conf)
    {
        $this->setUrl($conf);
        if (!empty ($this->url)) {
            try {
                $this->getControllerFromUrl();
            } catch (\Exception $e) {
                echo "Problème : ". $e->getMessage();
            }
        }
    }

    private function getControllerFromUrl() {
        $filename = "Site\\Controller\\".ucfirst($this->url)."\\".ucfirst($this->url)."Controller";
        new $filename;
    }
}