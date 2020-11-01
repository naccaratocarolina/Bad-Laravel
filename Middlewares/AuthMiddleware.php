<?php
namespace Middleware;
require_once "Middlewares/Middleware.php";
require_once "Models/Auth.php";
use Models\Auth;

class AuthMiddleware implements Middleware {
    public function handle(\Request $request, \Handler $next)
    {
        // Pega a header de Authorization
        $headers = apache_request_headers();
        $authHeader = $headers['Authorization'];
        if(isset($authHeader)) {
            // Extrai o token da Header
            $token = explode(" ", $authHeader)[1];
            // Verifica a autenticidade do token
            // Se for valido, passa o request
            if (Auth::verifyToken($token)) {
                $next($request);
            }
            // Se nao, interrompe o request e retorna uma mensagem de erro
            else {
                ob_get_clean();
                http_response_code(500);
                echo("O token dado não é válido!");
                throw new \Exception("O token dado não é válido!");
                die();
            }
        }
        // Caso a header de authorization nao seja passada
        else {
            ob_get_clean();
            http_response_code(500);
            echo("Voce nao tem permissao!");
            throw new \Exception("Voce nao tem permissao!");
            die();
        }
    }
}