<?php

namespace App;

class Request
{
    public $body = [];
    public $params = [];
    public function __construct()
    {
        $this->body = $this->getRequestBody();
        $this->params = $this->getParams();
    }

    private function getRequestBody()
    {
        $json = file_get_contents('php://input');
        $object = json_decode($json);

        return $object ?? [];
    }
    private function getParams()
    {
        // $arrData = [];
        // $queryString =  $_SERVER['QUERY_STRING'];
        // if (empty($queryString)) return $arrData;
        // $arr = explode("&", $queryString);

        // foreach ($arr as $r) {
        //     $stringToSplit = explode("=", $r);
        //     $arrData[$stringToSplit[0]] = $stringToSplit[1];
        // }
        // return $arrData;
    }
    function getCurrentPathParams($path)
    {
        $currentRoute = preg_match_all("/:\w+/i", $path, $matches);
        // return $matches ?? null;
        $returns = [];
        foreach ($matches as $match) {
            foreach ($match as $m) $returns[] = $m;
        }
        return $currentPath ?? [];
    }
}