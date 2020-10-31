<?php
namespace Middleware;
require_once "Middlewares/Middleware.php";
require_once "Models/Auth.php";
use Models\Auth;

class AuthMiddleware implements Middleware {
    public function handle(\Request $request, \Handler $next)
    {
        $headers = apache_request_headers();
        $authHeader = $headers['Authorization'];
        if(isset($authHeader)) {
            $token = explode(" ", $authHeader)[1];
            if (Auth::verifyToken($token)) {
                $next($request);
            }
            else {
                ob_get_clean();
                http_response_code(500);
                echo("O token dado não é válido!");
                throw new \Exception("O token dado não é válido!");
                die();
            }
        }
        else {
            ob_get_clean();
            http_response_code(500);
            echo("Voce nao tem permissao!");
            throw new \Exception("Voce nao tem permissao!");
            die();
        }
    }
}