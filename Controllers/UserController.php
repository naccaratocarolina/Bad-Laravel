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
use JsonResponse;

class UserController{

    /**
     * Lista todos os usuários do banco de dados.
     *
     * @param Request $request
     */
    static public function index(Request $request){
        $users = User::all();
        $headers = ["Accept" => "application/json"];
        response($users,200,$headers)->send();
    }

    /**
     * Encontra determinado usuário em funcao da query dada como parametro.
     *
     * @param Request $request
     */
    static public function show(Request $request) {
        $user = User::find($request);
        $headers = ["Accept" => "application/json"];
        response(["user" => $user],200,$headers)->send();
    }

    /**
     * Cria um novo usuário.
     *
     * @param Request $request
     */
    static public function create(Request $request){
        $user = User::create($request);
        $headers = ["Accept" => "application/json"];
        response($user,201,$headers)->send();
    }

    /**
     * Edita um usuário já existente.
     *
     * @param Request $request
     */
    static public function update(Request $request){
        $user = User::update($request);
        $headers = ["Accept" => "application/json"];
        response($user,200,$headers)->send();
    }

    /**
     * Deleta um usuário do banco de dados.
     *
     * @param Request $request
     */
    static public function delete(Request $request){
        $deleted = User::delete($request);
        $headers = ["Accept" => "application/json"];
        response($deleted,200,$headers)->send();
    }
} 