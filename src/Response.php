<?php

namespace App;

class Response
{
    public function statusCode($code)
    {
        http_response_code($code);
        return $this;
    }

    public function send($data)
    {
        echo (json_encode($data));
    }
    public function json($data)
    {
        if (!is_array($data) && !is_object($data)) throw new \Exception("This function only accesses arrays or objects, " . gettype($data) . " given", 1);
        echo (json_encode($data));
    }
    public function session($args = []): void
    {
        session_set_cookie_params($args['expires_at'] ?? null, $args['path'] ?? "/", $args['domain'] ?? null,  $args['httponly'] ?? false, $args['secure'] ?? false); //uncomment this when on production
        session_start();
        session_regenerate_id(true);

        if ($args['data'] != null) {
            foreach ($args['data'] as $key => $value) {
                $_SESSION[$key] = $value;
            }
        }
    }
}