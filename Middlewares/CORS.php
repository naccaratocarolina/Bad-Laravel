<?php
namespace Middleware;
require_once "Middlewares/Middleware.php";

class CORS implements Middleware{

    public function handle(\Request $request,\Handler $next){

        $next($request);
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers:', "Accept, Authorization, Content-Type");
    }
}