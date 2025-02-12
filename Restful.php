<?php
    class Controller
    {
        private $pathInfo;
        private $URLSegment;

        function __construct()
        {
            if(!isset($_SERVER['PATH_INFO']))
            {
                exit;    
            }

            $this->pathInfo = $_SERVER['PATH_INFO'];
            $this->URLSegment = explode('/', $this -> pathInfo);
            array_shift($this->URLSegment);

            $resourceName = array_shift($this->URLSegment);
            $serviceName = ucfirst($resourceName."Service");

            $fileName = $serviceName.".php";

            if(!file_exists($fileName))
            {
                exit;
            }

            include_once($fileName);
            $service = new $serviceName;
            $method = $_SERVER["REQUEST_METHOD"];
            $service->$method($this->URLSegment);
        }
    }

    $controller = new Controller();
?>