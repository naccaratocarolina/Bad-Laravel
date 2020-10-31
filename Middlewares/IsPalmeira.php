<?php
namespace Middleware;
require_once "Middlewares/Middleware.php";

class IsPalmeira implements Middleware{

    public function handle(\Request $request,\Handler $next){
        if($request->name === "Palmeira"){
            $next($request);
        } else {
            ob_get_clean();
            http_response_code(500);
            echo("Não é o Palmeira");
            throw new \Exception("Não é o Palmeira");
            die();
        }
    }
}