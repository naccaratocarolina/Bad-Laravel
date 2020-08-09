<?php
namespace Router;
require_once 'Controllers/UserController.php';
require_once 'Request.php';
require_once 'Middlewares/IsPalmeira.php';
require_once 'Handler.php';
require_once "Middlewares/CORS.php";

use Controllers\UserController;

class Route{
    private static $get_routes = [];
    private static $post_routes = [];
    private static $middlewares = ["CORS"];//,"IsPalmeira"];

    static public function get(string $url,string $controllerMethod){
        self::$get_routes[$url] = $controllerMethod;
    }
    
    static public function post(string $url,string $controllerMethod){
        self::$post_routes[$url] = $controllerMethod;
    }

    static public function handle(){
        $url = $_SERVER["REQUEST_URI"];
        $path = parse_url($url, PHP_URL_PATH);
        switch($_SERVER["REQUEST_METHOD"]){
            case "GET":
                if(!isset(self::$get_routes[$path])){
                    http_response_code(404);
                    echo 'NOT FOUND';
                    die();    
                }
                $function = explode("@",self::$get_routes[$path]);
                $request = new \Request($_GET);
                $handler = new \Handler(self::$middlewares,$function);
                $handler($request);
                break;
            case "POST":
                if(!isset(self::$post_routes[$path])){
                    http_response_code(404);
                    echo 'NOT FOUND';
                    die();    
                }
                $function = explode("@",self::$post_routes[$path]);
                $request = new \Request($_POST);
                $handler = new \Handler(self::$middlewares,$function);
                $handler($request);
                break;
            case "OPTIONS":
                $request = new \Request($_POST);
                $handler = new \Handler(self::$middlewares,function(){});
                $handler($request);
                http_response_code(204);
                break;
            default: 
                http_response_code(405);
                throw new \Exception("Method Not Suported");
                die();
        }
    }
};
