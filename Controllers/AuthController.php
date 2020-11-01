<?php
namespace Controllers;
require_once "DB.php";
require_once "Request.php";
require_once "Models/User.php";
require_once "Models/Auth.php";
require_once "JsonResponse.php";

use Models\Auth;
use Models\User;
use Request;

class AuthController {
    /**
     * Registra um novo usuário na plataforma.
     *
     * @param Request $request
     */
    static public function register(Request $request) {
        $user = User::create($request);
        $headers = ["Accept" => "application/json"];
        if (isset($user)) {
            response(["success" => "Usuário registrado com sucesso!"], 201, $headers)->send();
        } else {
            response(["err" => "Algo deu errado!"], 201, $headers)->send();
        }
    }

    /**
     * Loga um usuário já existente na plataforma, gerando um JWT.
     *
     * @param Request $request
     */
    static public function login(Request $request) {
        $user = User::find($request);
        $headers = ["Accept" => "application/json"];
        if (Auth::attempt($request)) {
            $token = $user->createToken();
            response(["token" => $token, "success" => "Usuário logado com sucesso!"], 201, $headers)->send();
        }
        else {
            response(["err" => "Algo deu errado!"], 401, $headers)->send();
        }
    }

    /**
     * Funcao que retorna detalhes do usuário logado.
     * Passar a header de autorizacao com o token para usar essa rota!
     */
    static public function getDetails() {
        $user = Auth::user();
        $headers = ["Accept" => "application/json"];
        response(["user" => $user], 201, $headers)->send();
    }
}