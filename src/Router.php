<?php

namespace App;

class Router
{
    private array $handlers;
    private $notFoundHandler;
    private const METHOD_POST = "POST";
    private const METHOD_GET = "GET";
    private $routePath;

    public function get(string $path, $handler)
    {
        $this->addHandlers(self::METHOD_GET, $this->routePath ? $this->routePath . $path : $path, $handler);
        return $this;
    }

    public function post(string $path, $handler)
    {
        $this->addHandlers(self::METHOD_POST, $this->routePath ? $this->routePath . $path : $path, $handler);
        return $this;
    }

    public function addNotFoundHandler($handler): void
    {
        $this->notFoundHandler = $handler;
    }
    private function addHandlers(string $method, string $path, $handler): void
    {
        $this->handlers[$method . $path] = [
            'path' => $path,
            'handler' => $handler,
            'method' => $method
        ];
    }

    public function run()
    {
        $request = new Request();
        $response = new Response();
        $requestUri = parse_url($_SERVER['REQUEST_URI']);
        $requestPath = $requestUri['path'];
        $callback = null;

        foreach ($this->handlers as $handler) {
            $method = $_SERVER['REQUEST_METHOD'];
            $cutUrls = arrangeArray((explode("/", $handler['path'])));
            $path =  arrangeArray(($request->getCurrentPathParams));

            if (count($path) !== count($cutUrls)) continue;
            if (preg_match_all("/:\w+/i", $handler['path'], $matches)) {

                //    find and replace all :param with the value from the
                $request->params = [];
                for ($i = 0; $i < count($cutUrls); $i++) {
                    if (preg_match("/:\w+/i", $cutUrls[$i], $match)) {
                        // Store params from query string
                        $param = substr($match[0], 1);
                        $request->params[$param] = $path[$i];
                    }
                    $handler['path'] = str_replace(($cutUrls[$i]), ($path[$i]), $handler['path']);
                }
            }
            if ($handler['path'] === $requestPath && $handler['method'] === $method) {
                $callback = $handler['handler'];
            }
        }

        if (!$callback) {
            http_response_code(404);
            if (!empty($this->notFoundHandler)) {
                $callback = $this->notFoundHandler;
            }
        }
        call_user_func_array($callback, [
            $request, $response
        ]);
    }
}