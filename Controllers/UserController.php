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
    
    static public function index(Request $request){
        $users = User::all();
        $headers = ["Accept" => "application/json"];
        response($users,200,$headers)->send();
    }

    static public function show(Request $request) {
        $user = User::find($request);
        $headers = ["Accept" => "application/json"];
        response(["user" => $user],200,$headers)->send();
    }

    static public function update(Request $request){
        //echo 'sadasd';
        $user = User::update($request);
        //header("Content-type: application/json");
        $headers = ["Accept" => "application/json"];
        response($user,200,$headers)->send();
    }

    static public function delete(Request $request){
        $deleted = User::delete($request);
        //header("Content-type: application/json");
        $headers = ["Accept" => "application/json"];
        response($deleted,200,$headers)->send();
    }
    
    static public function create(Request $request){
        $user = User::create($request);
        //header("Content-type: application/json");
        $headers = ["Accept" => "application/json"];
        response($user,201,$headers)->send();
    }

    static public function register(Request $request) {
        $user = User::create($request);
        $headers = ["Accept" => "application/json"];
        if (isset($user)) {
            response(["success" => "Usuário registrado com sucesso!"], 201, $headers)->send();
        } else {
            response(["err" => "Algo deu errado!"], 201, $headers)->send();
        }
    }

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
} 